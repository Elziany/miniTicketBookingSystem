<x-filament-panels::page>
    {{-- Load ZXing Browser Library for Reliable Scanning --}}
    <script src="https://unpkg.com/@zxing/library@latest"></script>

    <div style="display: flex; flex-direction: column; gap: 1.5rem;">

        {{-- QR Code Scanner Section --}}
        <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 1rem; border-bottom: 1px solid #f3f4f6; padding-bottom: 0.5rem;">
                QR Code Scanner
            </h2>

            <div style="display: flex; flex-direction: column; align-items: center; gap: 1rem;">
                {{-- Video Element --}}
                <div style="width: 100%; max-width: 450px; border-radius: 0.5rem; overflow: hidden; border: 1px solid #e5e7eb; background-color: #000000; position: relative;">
                    <video id="video" style="width: 100%; height: auto; display: block;"></video>
                </div>

                {{-- Status Output --}}
                <div id="scanner-status" style="font-size: 0.875rem; color: #4b5563; font-weight: 600; text-align: center;">
                    Status: Idle
                </div>

                <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap;">
                    <button
                        type="button"
                        id="start-btn"
                        style="background-color: #10b981; color: #ffffff; font-weight: 600; font-size: 0.875rem; padding: 0.5rem 1rem; border-radius: 0.375rem; border: none; cursor: pointer;"
                    >
                        Start Scanner
                    </button>

                    <button
                        type="button"
                        id="stop-btn"
                        style="background-color: #ef4444; color: #ffffff; font-weight: 600; font-size: 0.875rem; padding: 0.5rem 1rem; border-radius: 0.375rem; border: none; cursor: pointer; display: none;"
                    >
                        Stop Scanner
                    </button>
                </div>
            </div>
        </div>

            
        {{-- Manual Check-in Section --}}
        <div style="background-color: #ffffff; border: 1px solid #e5e7eb; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);">
            <h2 style="font-size: 1.125rem; font-weight: 700; color: #111827; margin-bottom: 1rem; border-bottom: 1px solid #f3f4f6; padding-bottom: 0.5rem;">
                Manual Check-in
            </h2>

            <form wire:submit="checkInManually" style="display: flex; flex-direction: column; gap: 1rem;">
                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label for="reservationReference" style="font-size: 0.875rem; font-weight: 600; color: #374151;">
                        Reservation Reference <span style="color: #ef4444;">*</span>
                    </label>
                    <input
                        type="text"
                        id="reservationReference"
                        wire:model="reservationReference"
                        placeholder="e.g. RES-12345"
                        style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; color: #111827; background-color: #ffffff; outline: none; box-sizing: border-box;"
                    />
                    @error('reservationReference')
                        <span style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem; font-weight: 500;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="display: flex; flex-direction: column; gap: 0.25rem;">
                    <label for="manualNote" style="font-size: 0.875rem; font-weight: 600; color: #374151;">
                        Manual Note <span style="color: #ef4444;">*</span>
                    </label>
                    <textarea
                        id="manualNote"
                        wire:model="manualNote"
                        rows="3"
                        placeholder="Reason for manual check-in..."
                        style="width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #d1d5db; border-radius: 0.375rem; font-size: 0.875rem; color: #111827; background-color: #ffffff; outline: none; box-sizing: border-box;"
                    ></textarea>
                    @error('manualNote')
                        <span style="font-size: 0.75rem; color: #ef4444; margin-top: 0.25rem; font-weight: 500;">{{ $message }}</span>
                    @enderror
                </div>

                <div style="margin-top: 0.5rem;">
                    <button
                        type="submit"
                        style="background-color: #2563eb; color: #ffffff; font-weight: 600; font-size: 0.875rem; padding: 0.5rem 1rem; border-radius: 0.375rem; border: none; cursor: pointer;"
                    >
                        Check In Guest
                    </button>
                </div>
            </form>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let codeReader = null;
            let selectedDeviceId = null;
            let isProcessing = false;

            const startBtn = document.getElementById('start-btn');
            const stopBtn = document.getElementById('stop-btn');
            const statusDiv = document.getElementById('scanner-status');

            function updateStatus(message, isError = false) {
                statusDiv.innerText = 'Status: ' + message;
                statusDiv.style.color = isError ? '#ef4444' : '#4b5563';
            }

            async function processQrCheckIn(reservationReference) {
                updateStatus('Processing check-in for ' + reservationReference + '...');
                try {
                    // Fetch the exact parent Livewire page component instance using Blade rendering
                    const pageComponent = window.Livewire.find('{{ $this->getId() }}');

                    if (pageComponent) {
                        await pageComponent.checkInByQr(reservationReference);
                        updateStatus('Done processing.');
                    } else {
                        updateStatus('Livewire page component not found!', true);
                    }
                } catch (err) {
                    updateStatus('Check-in error: ' + (err.message || err), true);
                }
            }

            startBtn.addEventListener('click', async function () {
                if (typeof ZXing === 'undefined') {
                    updateStatus('ZXing library not loaded!', true);
                    return;
                }

                if (!codeReader) {
                    codeReader = new ZXing.BrowserQRCodeReader();
                }

                updateStatus('Accessing camera...');

                try {
                    const videoInputDevices = await codeReader.getVideoInputDevices();

                    if (videoInputDevices.length === 0) {
                        updateStatus('No camera found on this device.', true);
                        return;
                    }

                    // Prefer back/rear environment camera
                    const backCamera = videoInputDevices.find(device => 
                        device.label.toLowerCase().includes('back') || 
                        device.label.toLowerCase().includes('environment')
                    );

                    selectedDeviceId = backCamera ? backCamera.deviceId : videoInputDevices[0].deviceId;

                    startBtn.style.display = 'none';
                    stopBtn.style.display = 'inline-block';
                    updateStatus('Scanner active. Point full QR code at the camera.');

                    codeReader.decodeFromVideoDevice(selectedDeviceId, 'video', async (result, err) => {
                        if (result && !isProcessing) {
                            isProcessing = true;
                            updateStatus('QR Code Detected: ' + result.text);

                            // Reset video decode session
                            codeReader.reset();
                            startBtn.style.display = 'inline-block';
                            stopBtn.style.display = 'none';

                            await processQrCheckIn(result.text);
                            isProcessing = false;
                        }
                    });

                } catch (err) {
                    updateStatus('Camera permission error: ' + err, true);
                }
            });

            stopBtn.addEventListener('click', function () {
                if (codeReader) {
                    codeReader.reset();
                }
                startBtn.style.display = 'inline-block';
                stopBtn.style.display = 'none';
                updateStatus('Scanner stopped.');
            });
        });
    </script>
</x-filament-panels::page>