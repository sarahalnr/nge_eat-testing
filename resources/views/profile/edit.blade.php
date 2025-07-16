@extends('layouts.navigation')

@section('content')
<div class="py-12" style="padding: 24px;">
    <div style="max-width: 1120px; margin: auto; padding: 0 16px; display: flex; flex-direction: column; gap: 24px;">
        
        {{-- Bagian 2 form di atas (Update Profile dan Update Password) --}}
        <div style="display: flex; flex-wrap: wrap; gap: 24px;">
            {{-- Card untuk Update Profile --}}
            <div style="flex: 1; min-width: 300px; background: #fff; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); border-radius: 12px; padding: 32px;">
                <div style="max-width: 480px; margin: auto;">
                    @include('profile.partials.update-profile-information-form')  {{-- Menggunakan partial untuk form update profile --}}
                </div>
            </div>

            {{-- Card untuk Update Password --}}
            <div style="flex: 1; min-width: 300px; background: #fff; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); border-radius: 12px; padding: 32px;">
                <div style="max-width: 480px; margin: auto;">
                    @include('profile.partials.update-password-form')  {{-- Menggunakan partial untuk form update password --}}
                </div>
            </div>
        </div>

        {{-- Bagian Delete User --}}
        <div style="background: #fff; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); border-radius: 12px; padding: 24px;">
            <div style="max-width: 420px; margin: auto;">
                @include('profile.partials.delete-user-form')  {{-- Menggunakan partial untuk form delete user --}}
            </div>
        </div>

    </div>
</div>
@endsection
