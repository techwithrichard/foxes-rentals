<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Properties for Rent</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <div class="container">
        <h1>Properties for Rent</h1>
        <div class="property-grid">
            @foreach($properties as $property)
                <div class="property-card" onclick="window.location.href='{{ route('properties.show', $property->id) }}'">
                    <img src="{{ $property->images->first()->url }}" alt="{{ $property->name }}">
                    <div class="property-info">
                        <h2>{{ $property->name }}</h2>
                        <p>{{ $property->description }}</p>
                        <span>Rent: {{ $property->rent }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
