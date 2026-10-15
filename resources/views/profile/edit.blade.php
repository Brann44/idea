<x-layout>
    <div class="py-8 max-w-2xl mx-auto space-y-6">
        <div>
            <h1 class="text-3xl font-bold">Profile</h1>
            <p class="text-muted-foreground text-sm mt-2">Manage your account settings and preferences.</p>
        </div>

        @if (session('status'))
            <div class="p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-500 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <x-card>
            <form method="POST" action="{{ route('profile.update') }}" class="space-y-6">
                @csrf
                @method('PATCH')

                {{-- Nombre --}}
                <x-form.field 
                    label="Name" 
                    name="name" 
                    :value="old('name', $user->name)" 
                    required 
                />

                {{-- Correo --}}
                <x-form.field 
                    label="Email" 
                    name="email" 
                    type="email" 
                    :value="old('email', $user->email)" 
                    required 
                />

                {{-- Nueva Contraseña (opcional) --}}
                <x-form.field 
                    label="New Password" 
                    name="password" 
                    type="password" 
                    placeholder="Leave blank to keep current password"
                />

                {{-- Confirmar Contraseña --}}
                <x-form.field 
                    label="Confirm New Password" 
                    name="password_confirmation" 
                    type="password" 
                />

                <div class="flex justify-end pt-2">
                    <button type="submit" class="btn">
                        Save Changes
                    </button>
                </div>
            </form>
        </x-card>
    </div>
</x-layout>
