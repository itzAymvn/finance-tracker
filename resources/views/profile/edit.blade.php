@extends('layouts.app')
@section('title', 'Profile')
@section('content')

<div class="page-header">
    <div>
        <h1>Profile</h1>
        <p>Manage your account settings</p>
    </div>
</div>

<div class="max-w-2xl mx-auto space-y-6">
    <div class="bg-surface-card dark:bg-surface-dark-card rounded-xl border border-border dark:border-border-dark shadow-sm p-6">
        @include('profile.partials.update-profile-information-form')
    </div>
    <div class="bg-surface-card dark:bg-surface-dark-card rounded-xl border border-border dark:border-border-dark shadow-sm p-6">
        @include('profile.partials.update-password-form')
    </div>
    <div class="bg-surface-card dark:bg-surface-dark-card rounded-xl border border-ruby/20 shadow-sm p-6">
        @include('profile.partials.delete-user-form')
    </div>
</div>

@endsection
