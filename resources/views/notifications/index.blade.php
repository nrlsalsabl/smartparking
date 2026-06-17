@extends('layouts.app')

@section('content')

<div class="container mt-4">

    <h3>Notifications</h3>

    <div class="card mt-3">
        <div class="card-body">

            @forelse($notifications as $notif)

                <div class="border-bottom py-3">

                    {{-- TITLE --}}
                    @php
                        $isAdminNotif = (stripos($notif->title, 'melakukan') !== false || stripos($notif->title, 'pembayaran') !== false) && auth()->user()->role->role_name == 'admin';
                    @endphp

                    @if($isAdminNotif)
                        {{-- Admin notification format - berbeda dari customer --}}
                        <div style="background-color: #f8f9fa; padding: 10px; border-left: 4px solid #007bff; margin-bottom: 10px;">
                            <div style="margin-bottom: 8px;">
                                <strong style="color: #007bff;">{{ $notif->title }}</strong>
                            </div>
                            <div style="color: #495057; font-size: 0.95rem; line-height: 1.8;">
                                {!! nl2br(e($notif->message)) !!}
                            </div>
                        </div>
                    @else
                        {{-- User notification format --}}
                        <strong>{{ $notif->title }}</strong>
                        <br>
                        <small style="color: #6c757d;">
                            {!! nl2br(e($notif->message)) !!}
                        </small>
                    @endif

                    <br>

                    {{-- STATUS --}}
                    @if(!$notif->is_read)
                        <span class="badge bg-danger">Unread</span>
                    @else
                        <span class="badge bg-success">Read</span>
                    @endif

                </div>

            @empty

                <p>No notifications</p>

            @endforelse

        </div>
    </div>

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>

</div>

@endsection