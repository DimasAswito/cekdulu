import { Html5Qrcode, Html5QrcodeSupportedFormats } from 'html5-qrcode';

window.barcodeScanner = function () {
    return {
        open: false,
        error: null,
        scanner: null,

        async start() {
            this.open = true;
            this.error = null;

            await this.$nextTick();

            this.scanner = new Html5Qrcode('barcode-reader');

            try {
                await this.scanner.start(
                    { facingMode: 'environment' },
                    {
                        fps: 10,
                        qrbox: { width: 260, height: 160 },
                        formatsToSupport: [
                            Html5QrcodeSupportedFormats.EAN_13,
                            Html5QrcodeSupportedFormats.EAN_8,
                            Html5QrcodeSupportedFormats.UPC_A,
                            Html5QrcodeSupportedFormats.UPC_E,
                            Html5QrcodeSupportedFormats.CODE_128,
                        ],
                    },
                    (decodedText) => this.onDecoded(decodedText),
                    () => {},
                );
            } catch (e) {
                this.error = 'Tidak bisa mengakses kamera. Pastikan izin kamera sudah diberikan.';
            }
        },

        async onDecoded(decodedText) {
            await this.stop();
            this.$wire.set('query', decodedText);
            this.$wire.searchByBarcode();
        },

        async stop() {
            if (this.scanner) {
                try {
                    await this.scanner.stop();
                    this.scanner.clear();
                } catch (e) {
                    // scanner was already stopped/cleared
                }
                this.scanner = null;
            }
            this.open = false;
        },
    };
};
