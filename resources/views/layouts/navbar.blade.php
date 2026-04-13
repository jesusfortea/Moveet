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
                    <p>Nombre</p>
                    <p>Puntos</p>
                    <p>Nivel</p>
                </div>

            </div>

        </div>

        <div class="col-span-3 flex w-full h-full items-center justify-around">

            <p>Inicio</p>
            <p>Evento</p>
            <p>Chat</p>
            <p>Pase de paseo</p>
            <p>Tienda</p>

        </div>


        <div class="items-center h-full w-full flex justify-center">
            <img src="{{ asset('img/Exit.png') }}" alt="No se ha podido cargar la imagen">
            <p>Salir</p>
        </div>

    </div>



</nav>