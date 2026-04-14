@extends('layouts.app')

@section('content')
<div id="admin-root" class="min-h-screen">
    <!-- Vue Root Logic -->
    <admin-root-selector></admin-root-selector>
</div>
@endsection

<script>
    // Note: We are moving the logic into a new root component for better state management
</script>
