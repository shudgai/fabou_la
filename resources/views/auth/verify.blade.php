@extends('layouts.app')

@section('content')
<div class="w-full max-w-lg mx-auto px-4 py-8">
    <div class="bg-white rounded-[32px] shadow-[0_20px_70px_rgba(0,0,0,0.05)] border border-slate-100 p-8">
        <h3 class="text-lg font-black text-slate-900 mb-4">{{ __('Verify Your Email Address') }}</h3>

        <div class="space-y-4">
            @if (session('resent'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 rounded-2xl flex items-center space-x-3 text-emerald-600" role="alert">
                    <span class="text-sm font-bold">{{ __('A fresh verification link has been sent to your email address.') }}</span>
                </div>
            @endif

            <p class="text-slate-600">{{ __('Before proceeding, please check your email for a verification link.') }}</p>
            <p class="text-slate-600">{{ __('If you did not receive the email') }},
                <form method="POST" action="{{ route('verification.resend') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-indigo-600 font-bold hover:underline p-0 bg-transparent border-none cursor-pointer">{{ __('click here to request another') }}</button>.
                </form>
            </p>
        </div>
    </div>
</div>
@endsection
