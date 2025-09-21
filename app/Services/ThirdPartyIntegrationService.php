<?php

namespace App\Services;

use App\Models\Property;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ThirdPartyIntegrationService
{
    /**
     * Integrate with Google Maps API
     */
    public function getPropertyLocationData(string $address): array
    {
        $cacheKey = 'google_maps_' . md5($address);
        
        return Cache::remember($cacheKey, 3600, function () use ($address) {
            try {
                $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                    'address' => $address,
                    'key' => config('services.google.maps_api_key')
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if ($data['status'] === 'OK' && !empty($data['results'])) {
                        $result = $data['results'][0];
                        
                        return [
                            'success' => true,
                            'latitude' => $result['geometry']['location']['lat'],
                            'longitude' => $result['geometry']['location']['lng'],
                            'formatted_address' => $result['formatted_address'],
                            'place_id' => $result['place_id'],
                            'address_components' => $result['address_components']
                        ];
                    }
                }

                return [
                    'success' => false,
                    'message' => 'Unable to geocode address'
                ];

            } catch (\Exception $e) {
                Log::error('Google Maps API error', [
                    'address' => $address,
                    'error' => $e->getMessage()
                ]);

                return [
                    'success' => false,
                    'message' => 'Geocoding service unavailable'
                ];
            }
        });
    }

    /**
     * Integrate with Twilio for SMS notifications
     */
    public function sendSmsNotification(string $phoneNumber, string $message): array
    {
        try {
            $response = Http::post('https://api.twilio.com/2010-04-01/Accounts/' . config('services.twilio.account_sid') . '/Messages.json', [
                'From' => config('services.twilio.from_number'),
                'To' => $phoneNumber,
                'Body' => $message
            ], [
                'auth' => [config('services.twilio.account_sid'), config('services.twilio.auth_token')]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('SMS sent successfully', [
                    'to' => $phoneNumber,
                    'message_id' => $data['sid']
                ]);

                return [
                    'success' => true,
                    'message_id' => $data['sid'],
                    'status' => $data['status']
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to send SMS'
            ];

        } catch (\Exception $e) {
            Log::error('Twilio SMS error', [
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'SMS service unavailable'
            ];
        }
    }

    /**
     * Integrate with SendGrid for email notifications
     */
    public function sendEmailNotification(string $to, string $subject, string $htmlContent, array $data = []): array
    {
        try {
            $response = Http::post('https://api.sendgrid.com/v3/mail/send', [
                'personalizations' => [
                    [
                        'to' => [['email' => $to]],
                        'dynamic_template_data' => $data
                    ]
                ],
                'from' => [
                    'email' => config('mail.from.address'),
                    'name' => config('mail.from.name')
                ],
                'subject' => $subject,
                'content' => [
                    [
                        'type' => 'text/html',
                        'value' => $htmlContent
                    ]
                ]
            ], [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.sendgrid.api_key')
                ]
            ]);

            if ($response->successful()) {
                Log::info('Email sent successfully', [
                    'to' => $to,
                    'subject' => $subject
                ]);

                return [
                    'success' => true,
                    'message' => 'Email sent successfully'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to send email'
            ];

        } catch (\Exception $e) {
            Log::error('SendGrid email error', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Email service unavailable'
            ];
        }
    }

    /**
     * Integrate with Stripe for payment processing
     */
    public function processStripePayment(array $paymentData): array
    {
        try {
            $response = Http::post('https://api.stripe.com/v1/payment_intents', [
                'amount' => $paymentData['amount'] * 100, // Convert to cents
                'currency' => $paymentData['currency'] ?? 'usd',
                'payment_method' => $paymentData['payment_method_id'],
                'confirmation_method' => 'manual',
                'confirm' => true,
                'metadata' => [
                    'property_id' => $paymentData['property_id'],
                    'tenant_id' => $paymentData['tenant_id']
                ]
            ], [
                'headers' => [
                    'Authorization' => 'Bearer ' . config('services.stripe.secret_key')
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Stripe payment processed', [
                    'payment_intent_id' => $data['id'],
                    'status' => $data['status']
                ]);

                return [
                    'success' => true,
                    'payment_intent_id' => $data['id'],
                    'status' => $data['status'],
                    'client_secret' => $data['client_secret']
                ];
            }

            return [
                'success' => false,
                'message' => 'Payment processing failed'
            ];

        } catch (\Exception $e) {
            Log::error('Stripe payment error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Payment service unavailable'
            ];
        }
    }

    /**
     * Integrate with PayPal for payment processing
     */
    public function processPayPalPayment(array $paymentData): array
    {
        try {
            // First, create the payment
            $response = Http::post('https://api.sandbox.paypal.com/v1/payments/payment', [
                'intent' => 'sale',
                'payer' => [
                    'payment_method' => 'paypal'
                ],
                'transactions' => [
                    [
                        'amount' => [
                            'total' => $paymentData['amount'],
                            'currency' => $paymentData['currency'] ?? 'USD'
                        ],
                        'description' => $paymentData['description'] ?? 'Property rent payment'
                    ]
                ],
                'redirect_urls' => [
                    'return_url' => $paymentData['return_url'],
                    'cancel_url' => $paymentData['cancel_url']
                ]
            ], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getPayPalAccessToken(),
                    'Content-Type' => 'application/json'
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('PayPal payment created', [
                    'payment_id' => $data['id'],
                    'state' => $data['state']
                ]);

                return [
                    'success' => true,
                    'payment_id' => $data['id'],
                    'approval_url' => $data['links'][1]['href'] ?? null,
                    'state' => $data['state']
                ];
            }

            return [
                'success' => false,
                'message' => 'PayPal payment creation failed'
            ];

        } catch (\Exception $e) {
            Log::error('PayPal payment error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'PayPal service unavailable'
            ];
        }
    }

    /**
     * Integrate with AWS S3 for file storage
     */
    public function uploadFileToS3(string $filePath, string $fileName, string $bucket = null): array
    {
        try {
            $bucket = $bucket ?? config('filesystems.disks.s3.bucket');
            $fileContent = file_get_contents($filePath);
            
            $response = Http::put("https://{$bucket}.s3.amazonaws.com/{$fileName}", $fileContent, [
                'headers' => [
                    'Authorization' => 'AWS4-HMAC-SHA256 ' . $this->getAwsSignature($fileName, $fileContent),
                    'Content-Type' => mime_content_type($filePath)
                ]
            ]);

            if ($response->successful()) {
                $url = "https://{$bucket}.s3.amazonaws.com/{$fileName}";
                
                Log::info('File uploaded to S3', [
                    'file_name' => $fileName,
                    'url' => $url
                ]);

                return [
                    'success' => true,
                    'url' => $url,
                    'file_name' => $fileName
                ];
            }

            return [
                'success' => false,
                'message' => 'File upload failed'
            ];

        } catch (\Exception $e) {
            Log::error('S3 upload error', [
                'file_name' => $fileName,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'File storage service unavailable'
            ];
        }
    }

    /**
     * Integrate with Slack for notifications
     */
    public function sendSlackNotification(string $channel, string $message, array $attachments = []): array
    {
        try {
            $response = Http::post('https://hooks.slack.com/services/' . config('services.slack.webhook_url'), [
                'channel' => $channel,
                'text' => $message,
                'attachments' => $attachments
            ]);

            if ($response->successful()) {
                Log::info('Slack notification sent', [
                    'channel' => $channel,
                    'message' => $message
                ]);

                return [
                    'success' => true,
                    'message' => 'Slack notification sent'
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to send Slack notification'
            ];

        } catch (\Exception $e) {
            Log::error('Slack notification error', [
                'channel' => $channel,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Slack service unavailable'
            ];
        }
    }

    /**
     * Integrate with Zoom for video calls
     */
    public function createZoomMeeting(array $meetingData): array
    {
        try {
            $response = Http::post('https://api.zoom.us/v2/users/me/meetings', [
                'topic' => $meetingData['topic'],
                'type' => 2, // Scheduled meeting
                'start_time' => $meetingData['start_time'],
                'duration' => $meetingData['duration'] ?? 60,
                'timezone' => $meetingData['timezone'] ?? 'UTC',
                'settings' => [
                    'host_video' => true,
                    'participant_video' => true,
                    'join_before_host' => false,
                    'mute_upon_entry' => true,
                    'watermark' => false,
                    'use_pmi' => false,
                    'approval_type' => 0
                ]
            ], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getZoomAccessToken(),
                    'Content-Type' => 'application/json'
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('Zoom meeting created', [
                    'meeting_id' => $data['id'],
                    'topic' => $data['topic']
                ]);

                return [
                    'success' => true,
                    'meeting_id' => $data['id'],
                    'join_url' => $data['join_url'],
                    'start_url' => $data['start_url'],
                    'password' => $data['password']
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to create Zoom meeting'
            ];

        } catch (\Exception $e) {
            Log::error('Zoom meeting creation error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'Zoom service unavailable'
            ];
        }
    }

    /**
     * Integrate with DocuSign for document signing
     */
    public function createDocuSignEnvelope(array $envelopeData): array
    {
        try {
            $response = Http::post('https://demo.docusign.net/restapi/v2.1/accounts/' . config('services.docusign.account_id') . '/envelopes', [
                'emailSubject' => $envelopeData['subject'],
                'documents' => $envelopeData['documents'],
                'recipients' => [
                    'signers' => $envelopeData['signers']
                ],
                'status' => 'sent'
            ], [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->getDocuSignAccessToken(),
                    'Content-Type' => 'application/json'
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                Log::info('DocuSign envelope created', [
                    'envelope_id' => $data['envelopeId'],
                    'status' => $data['status']
                ]);

                return [
                    'success' => true,
                    'envelope_id' => $data['envelopeId'],
                    'status' => $data['status']
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to create DocuSign envelope'
            ];

        } catch (\Exception $e) {
            Log::error('DocuSign envelope creation error', [
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => 'DocuSign service unavailable'
            ];
        }
    }

    /**
     * Get PayPal access token
     */
    private function getPayPalAccessToken(): string
    {
        $cacheKey = 'paypal_access_token';
        
        return Cache::remember($cacheKey, 3600, function () {
            $response = Http::asForm()->post('https://api.sandbox.paypal.com/v1/oauth2/token', [
                'grant_type' => 'client_credentials'
            ], [
                'auth' => [config('services.paypal.client_id'), config('services.paypal.client_secret')]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'];
            }

            throw new \Exception('Failed to get PayPal access token');
        });
    }

    /**
     * Get Zoom access token
     */
    private function getZoomAccessToken(): string
    {
        $cacheKey = 'zoom_access_token';
        
        return Cache::remember($cacheKey, 3600, function () {
            $response = Http::asForm()->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => config('services.zoom.account_id')
            ], [
                'auth' => [config('services.zoom.client_id'), config('services.zoom.client_secret')]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'];
            }

            throw new \Exception('Failed to get Zoom access token');
        });
    }

    /**
     * Get DocuSign access token
     */
    private function getDocuSignAccessToken(): string
    {
        $cacheKey = 'docusign_access_token';
        
        return Cache::remember($cacheKey, 3600, function () {
            $response = Http::asForm()->post('https://account-d.docusign.com/oauth/token', [
                'grant_type' => 'client_credentials',
                'scope' => 'signature'
            ], [
                'auth' => [config('services.docusign.client_id'), config('services.docusign.client_secret')]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['access_token'];
            }

            throw new \Exception('Failed to get DocuSign access token');
        });
    }

    /**
     * Get AWS signature for S3 upload
     */
    private function getAwsSignature(string $fileName, string $fileContent): string
    {
        // This is a simplified version - in production, you'd use AWS SDK
        $accessKey = config('filesystems.disks.s3.key');
        $secretKey = config('filesystems.disks.s3.secret');
        $region = config('filesystems.disks.s3.region');
        
        // Implementation would go here
        return 'simplified-signature';
    }

    /**
     * Test all third-party integrations
     */
    public function testIntegrations(): array
    {
        $results = [];

        // Test Google Maps
        $results['google_maps'] = $this->getPropertyLocationData('Nairobi, Kenya');

        // Test Twilio
        $results['twilio'] = $this->sendSmsNotification('+254712345678', 'Test SMS from Foxes Rentals');

        // Test SendGrid
        $results['sendgrid'] = $this->sendEmailNotification(
            'test@example.com',
            'Test Email',
            '<h1>Test Email from Foxes Rentals</h1>'
        );

        // Test Stripe
        $results['stripe'] = $this->processStripePayment([
            'amount' => 1000,
            'payment_method_id' => 'pm_test_123'
        ]);

        // Test Slack
        $results['slack'] = $this->sendSlackNotification(
            '#general',
            'Test notification from Foxes Rentals'
        );

        return $results;
    }
}
