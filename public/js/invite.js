document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const addContactForm = document.getElementById('add-contact-form');
    const addContactFeedback = document.getElementById('add-contact-feedback');
    const myQrModal = document.getElementById('my-qr-modal');
    const scanQrModal = document.getElementById('scan-qr-modal');
    const openMyQrButton = document.getElementById('open-my-qr');
    const openScanQrButton = document.getElementById('open-scan-qr');
    const closeMyQrButton = document.getElementById('close-my-qr-modal');
    const closeMyQrSecondary = document.getElementById('close-my-qr-btn');
    const closeScanQrButton = document.getElementById('close-scan-qr-modal');
    const copyQrLinkButton = document.getElementById('copy-qr-link');
    const copyCodeButton = document.getElementById('copy-code-btn');
    const inviteCodeSpan = document.getElementById('invite-code');
    const startScanButton = document.getElementById('start-scan-btn');
    const stopScanButton = document.getElementById('stop-scan-btn');
    const scanManualForm = document.getElementById('scan-manual-form');
    const scanManualInput = document.getElementById('scan-manual-input');
    const scannerStatus = document.getElementById('scanner-status');
    const qrVideo = document.getElementById('qr-video');
    const scannerPlaceholder = document.getElementById('scanner-placeholder');
    const qrScanUrl = window.chatConfig?.qrScanUrl;

    let mediaStream = null;
    let scanInterval = null;
    let detector = null;
    let isSubmittingScan = false;

    const setInlineFeedback = (message, isError = false) => {
        if (!addContactFeedback) {
            return;
        }

        addContactFeedback.hidden = false;
        addContactFeedback.textContent = message;
        addContactFeedback.classList.toggle('invite-inline-message--error', isError);
        addContactFeedback.classList.toggle('invite-inline-message--success', !isError);
    };

    const clearInlineFeedback = () => {
        if (!addContactFeedback) {
            return;
        }

        addContactFeedback.hidden = true;
        addContactFeedback.textContent = '';
        addContactFeedback.classList.remove('invite-inline-message--error', 'invite-inline-message--success');
    };

    const closeDialog = (dialog) => {
        if (!dialog) {
            return;
        }

        if (typeof dialog.close === 'function' && dialog.open) {
            dialog.close();
            return;
        }

        dialog.removeAttribute('open');
    };

    const openDialog = (dialog) => {
        if (!dialog || dialog.open) {
            return;
        }

        if (typeof dialog.showModal === 'function') {
            dialog.showModal();
            return;
        }

        dialog.setAttribute('open', 'open');
    };

    const setScannerStatus = (message, isError = false) => {
        if (!scannerStatus) {
            return;
        }

        scannerStatus.textContent = message;
        scannerStatus.classList.toggle('scanner-status--error', isError);
    };

    const stopScanner = () => {
        if (scanInterval) {
            window.clearInterval(scanInterval);
            scanInterval = null;
        }

        if (mediaStream) {
            mediaStream.getTracks().forEach((track) => track.stop());
            mediaStream = null;
        }

        if (qrVideo) {
            qrVideo.pause();
            qrVideo.srcObject = null;
        }

        if (scannerPlaceholder) {
            scannerPlaceholder.hidden = false;
        }
    };

    const submitQrValue = async (qrValue) => {
        if (!qrScanUrl || !csrfToken || isSubmittingScan) {
            return;
        }

        isSubmittingScan = true;
        setScannerStatus('Procesando código...', false);

        try {
            const response = await fetch(qrScanUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ qr_value: qrValue }),
            });

            const data = await response.json();
            const ok = Boolean(data.ok);

            setScannerStatus(data.message || 'Operación completada.', !ok);

            if (ok && data.redirect_url) {
                window.setTimeout(() => {
                    window.location.href = data.redirect_url;
                }, 800);
            }
        } catch (error) {
            console.error('No se pudo procesar el QR', error);
            setScannerStatus('No se pudo procesar el QR.', true);
        } finally {
            isSubmittingScan = false;
        }
    };

    const startScanner = async () => {
        if (!navigator.mediaDevices?.getUserMedia) {
            setScannerStatus('Este navegador no permite usar la cámara. Usa el campo manual.', true);
            return;
        }

        if (!('BarcodeDetector' in window)) {
            setScannerStatus('El escaneo automático no está disponible aquí. Usa el campo manual.', true);
            return;
        }

        try {
            detector = detector || new window.BarcodeDetector({ formats: ['qr_code'] });
            mediaStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment' },
                audio: false,
            });

            qrVideo.srcObject = mediaStream;
            await qrVideo.play();
            scannerPlaceholder.hidden = true;
            setScannerStatus('Buscando un QR de Moveet...');

            scanInterval = window.setInterval(async () => {
                if (isSubmittingScan || !qrVideo.videoWidth) {
                    return;
                }

                try {
                    const barcodes = await detector.detect(qrVideo);
                    const qrCode = barcodes.find((item) => item.rawValue);

                    if (!qrCode) {
                        return;
                    }

                    stopScanner();
                    await submitQrValue(qrCode.rawValue);
                } catch (error) {
                    console.error('Error al leer el QR', error);
                }
            }, 700);
        } catch (error) {
            console.error('No se pudo iniciar la cámara', error);
            setScannerStatus('No se pudo acceder a la cámara. Revisa los permisos.', true);
            stopScanner();
        }
    };

    if (addContactForm) {
        addContactForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            clearInlineFeedback();

            try {
                const response = await fetch(addContactForm.action, {
                    method: 'POST',
                    body: new FormData(addContactForm),
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const data = await response.json();
                const ok = Boolean(data.ok);

                setInlineFeedback(data.message || 'Operación completada.', !ok);

                if (ok) {
                    addContactForm.reset();

                    if (data.redirect_url && data.contact_id) {
                        window.setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 800);
                    }
                }
            } catch (error) {
                console.error('Error al enviar la solicitud', error);
                setInlineFeedback('No se pudo enviar la solicitud.', true);
            }
        });
    }

    openMyQrButton?.addEventListener('click', () => openDialog(myQrModal));
    closeMyQrButton?.addEventListener('click', () => closeDialog(myQrModal));
    closeMyQrSecondary?.addEventListener('click', () => closeDialog(myQrModal));

    copyQrLinkButton?.addEventListener('click', async function () {
        try {
            await navigator.clipboard.writeText(inviteCodeSpan?.dataset.link || '');
            copyQrLinkButton.textContent = 'Enlace copiado';
            window.setTimeout(() => {
                copyQrLinkButton.textContent = 'Copiar enlace';
            }, 1800);
        } catch (error) {
            console.error('No se pudo copiar el enlace', error);
        }
    });

    copyCodeButton?.addEventListener('click', async function () {
        try {
            await navigator.clipboard.writeText(inviteCodeSpan?.textContent || '');
            copyCodeButton.textContent = 'Copiado';
            window.setTimeout(() => {
                copyCodeButton.textContent = 'Copiar';
            }, 1800);
        } catch (error) {
            console.error('No se pudo copiar el texto', error);
        }
    });

    openScanQrButton?.addEventListener('click', () => {
        openDialog(scanQrModal);
        setScannerStatus('Preparado para escanear.');
    });

    closeScanQrButton?.addEventListener('click', () => {
        stopScanner();
        closeDialog(scanQrModal);
    });

    startScanButton?.addEventListener('click', startScanner);
    stopScanButton?.addEventListener('click', () => {
        stopScanner();
        setScannerStatus('Escaneo detenido.');
    });

    scanManualForm?.addEventListener('submit', async function (event) {
        event.preventDefault();

        const value = scanManualInput?.value.trim();

        if (!value) {
            setScannerStatus('Introduce un enlace o código válido.', true);
            return;
        }

        stopScanner();
        await submitQrValue(value);
    });

    [myQrModal, scanQrModal].forEach((dialog) => {
        dialog?.addEventListener('click', function (event) {
            if (event.target === dialog) {
                if (dialog === scanQrModal) {
                    stopScanner();
                }

                closeDialog(dialog);
            }
        });
    });

    scanQrModal?.addEventListener('close', stopScanner);
});
