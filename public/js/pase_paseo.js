window.onload = () => {
    // Inicialización de la vista del Pase de Paseo

    // Lógica para reclamar recompensas
    const rewardCards = document.querySelectorAll('.js-reclamar');

    rewardCards.forEach(card => {
        card.onclick = async () => {
            const isReclamable = card.getAttribute('data-reclamable') === 'true';
            const rewardId = card.getAttribute('data-id');
            const rewardNombre = card.getAttribute('data-nombre');

            if (!isReclamable) return;

            // Omitimos la confirmación explícita para agilizar la experiencia de usuario


            try {
                const response = await fetch(window.battlepassRoutes.reclamar.replace(':id', rewardId), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (response.ok) {
                    // Éxito: actualizar UI
                    card.setAttribute('data-reclamable', 'false');
                    card.closest('.reward-slot').classList.add('claimed');

                    // Añadir el badge de reclamado dinámicamente
                    const badge = document.createElement('div');
                    badge.className = 'claimed-badge';
                    badge.innerText = '✅ Reclamado';
                    card.appendChild(badge);

                    // El feedback visual (badge) es suficiente por ahora sin usar alertas invasivas

                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                console.error('Error al reclamar:', error);
                alert('Ocurrió un error al procesar la reclamación.');
            }
        });
    });

    // Centrar el track en el nivel actual del usuario
    const trackContainer = document.querySelector('.battlepass-track-container');
    const currentLevelCol = document.querySelector('.level-column.current-level');

    if (currentLevelCol && trackContainer) {
        const offsetLeft = currentLevelCol.offsetLeft;
        trackContainer.scrollTo({
            left: offsetLeft - 200,
            behavior: 'smooth'
        });
    }
};
