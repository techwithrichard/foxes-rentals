@echo off
echo Getting current ngrok URL...
curl -s http://localhost:4040/api/tunnels > temp_ngrok.json
for /f "tokens=*" %%i in ('powershell -command "(Get-Content temp_ngrok.json | ConvertFrom-Json).tunnels[0].public_url"') do set NGROK_URL=%%i
echo Current ngrok URL: %NGROK_URL%
echo.

if not exist .env (
    echo .env file not found. Creating from env_content.txt...
    copy env_content.txt .env
    echo .env file created successfully.
    echo.
)

echo Updating .env file with new ngrok URLs...
powershell -command "(Get-Content .env) -replace 'APP_URL=https://[^/]+\.ngrok-free\.app', 'APP_URL=%NGROK_URL%' | Set-Content .env"
powershell -command "(Get-Content .env) -replace 'MPESA_CONFIRMATION_URL=https://[^/]+\.ngrok-free\.app/api/callback/confirmation', 'MPESA_CONFIRMATION_URL=%NGROK_URL%/api/callback/confirmation' | Set-Content .env"
powershell -command "(Get-Content .env) -replace 'MPESA_VALIDATION_URL=https://[^/]+\.ngrok-free\.app/api/callback/validation', 'MPESA_VALIDATION_URL=%NGROK_URL%/api/callback/validation' | Set-Content .env"
powershell -command "(Get-Content .env) -replace 'MPESA_STK_CALLBACK_URL=https://[^/]+\.ngrok-free\.app/api/callback/stk_callback', 'MPESA_STK_CALLBACK_URL=%NGROK_URL%/api/callback/stk_callback' | Set-Content .env"

echo.
echo Updated URLs in .env:
echo APP_URL=%NGROK_URL%
echo MPESA_CONFIRMATION_URL=%NGROK_URL%/api/callback/confirmation
echo MPESA_VALIDATION_URL=%NGROK_URL%/api/callback/validation
echo MPESA_STK_CALLBACK_URL=%NGROK_URL%/api/callback/stk_callback
echo.
echo Clearing Laravel config cache...
php artisan config:clear
echo.
echo Done! Your MPesa URLs have been updated automatically.
echo.
del temp_ngrok.json
pause
