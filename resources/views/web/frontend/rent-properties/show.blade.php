<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $property->name }}</title>
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <div class="container">
        <h1>{{ $property->name }}</h1>
        <div class="property-details">
            <img src="{{ $property->images->first()->url }}" alt="{{ $property->name }}">
            <div class="property-info">
                <p><strong>Description:</strong> {{ $property->description }}</p>
                <p><strong>Type:</strong> {{ $property->type }}</p>
                <p><strong>Rent:</strong> {{ $property->rent }}</p>
                <p><strong>Deposit:</strong> {{ $property->deposit }}</p>
                <p><strong>Status:</strong> {{ $property->is_vacant ? 'Vacant' : 'Occupied' }}</p>
                <p><strong>Address:</strong> {{ $property->address->city }}, {{ $property->address->state }}</p>
            </div>
        </div>
        <a href="{{ url('/rent-properties') }}">Back to Properties</a>
    </div>
</body>
</html>
