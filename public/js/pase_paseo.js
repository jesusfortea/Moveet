(function () {
    // Inicialización de la vista del Pase de Paseo
    const initBus = () => {
        // Lógica para reclamar recompensas
        const rewardCards = document.querySelectorAll('.js-reclamar');
        console.log("Found rewardCards: " + rewardCards.length);

        rewardCards.forEach(card => {
            card.onclick = async () => {
                const isReclamable = card.getAttribute('data-reclamable') === 'true';
                const rewardId = card.getAttribute('data-id');
                const rewardNombre = card.getAttribute('data-nombre');

                console.log("Click en recompensa:", rewardId, "Reclamable:", isReclamable);

                if (!isReclamable) {
                    const isClaimed = card.closest('.reward-slot').classList.contains('claimed');
                    const isLocked = card.closest('.reward-slot').classList.contains('locked');
                    const isPremiumLevel = card.getAttribute('data-is-premium') === 'true';

                    if (isClaimed) {
                        alert('Ya has reclamado esta recompensa.');
                    } else if (isLocked) {
                        alert('Aún no tienes el nivel necesario para esta recompensa.');
                    } else if (isPremiumLevel && !document.querySelector('.subscribe-btn')) {
                        // if subscribe-btn is hidden it might mean they are premium, but another check can be used.
                        // Actually, safer to just say it's premium if it fails the other checks.
                        alert('Debes ser Premium para reclamar esta recompensa.');
                    } else {
                        // alert('No puedes reclamar esta recompensa todavía.');
                    }
                    return;
                }

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

                    } else {
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    console.error('Error al reclamar:', error);
                    alert('Ocurrió un error de red al procesar la reclamación.');
                }
            };
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

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initBus);
    } else {
        initBus();
    }
})();
