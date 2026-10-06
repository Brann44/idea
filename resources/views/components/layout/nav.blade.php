<nav class="border-b border-border px-6">
    <div class="max-w-7xl mx-auto h-16 flex items-center justify-between">
        <div>
            <a href="/">
                <img src="/images/imagen3.png" alt="" width="110" alt="Idea logo">
            </a>
        </div>

        <div class="flex gap-x-5 items-center">
            @auth

                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="btn">Cerrar Sesión</button>
                </form>
                
            @endauth
            
            @guest
                <a href="/login" class="hover:text-primary transition-colors">Iniciar Sesión</a>
                <a href="/register" class="btn">Registrarse</a>
            @endguest
        </div>
    </div>
</nav>