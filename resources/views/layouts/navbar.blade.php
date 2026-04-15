<nav class="bg-[#8FA8A6] w-full h-[15vh] fixed">

    <div class="grid grid-cols-6">

        <div class="col-span-2 grid grid-cols-3">

            <div>

                <img class="h-[15vh] p-2" src="{{ asset('img/LogoUsarDiaDia.png') }}" alt="No se ha podido cargar la imagen">

            </div>


            <div class="col-span-2 grid grid-cols-3 items-center">

                {{-- Imagen del usuario --}}
                <div class="bg-white border border-gray-200 rounded-full w-[70px] h-[12vh]">
                    <img src="" alt="">
                </div>

                {{-- Info del user --}}
                <div class="col-span-2">
                    <p class="font-bold text-white">{{ Auth::user()->name ?? 'Usuario' }}</p>
                    <p class="text-white text-sm">{{ Auth::user()->puntos ?? 0 }} Puntos</p>
                    <p class="text-white text-sm">Nivel {{ Auth::user()->nivel ?? 1 }}</p>
                </div>

            </div>

        </div>

        <div class="col-span-3 flex w-full h-full items-center justify-around">

            <a href="{{ route('home') }}">Inicio</a>
            <a>Evento</a>
            <a>Chat</a>
            <a>Pase de paseo</a>
            <a>Tienda</a>

        </div>


        <a class="items-center h-full w-full flex justify-center">
            <img src="{{ asset('img/Exit.png') }}" alt="No se ha podido cargar la imagen">
            <p>Salir</p>
        </a>

    </div>



</nav>