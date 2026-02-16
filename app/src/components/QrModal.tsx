import { useEffect, useState } from 'react';
import QRCode from 'qrcode';

type QrModalProps = {
  open: boolean;
  onClose: () => void;
  value: string;
};

const QrModal = ({ open, onClose, value }: QrModalProps) => {
  const [src, setSrc] = useState<string>('');

  useEffect(() => {
    let active = true;
    const generate = async () => {
      if (!open || !value) {
        setSrc('');
        return;
      }
      const url = await QRCode.toDataURL(value, { width: 320, margin: 1 });
      if (active) {
        setSrc(url);
      }
    };

    void generate();
    return () => {
      active = false;
    };
  }, [open, value]);

  if (!open) {
    return null;
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 px-6">
      <div className="w-full max-w-sm rounded-2xl border border-[#1716132e] bg-[#f7f2e6] p-6 text-[#171613] shadow-[0_20px_40px_rgba(0,0,0,0.25)]">
        <h2 className="text-xl font-semibold">Scan to join this room</h2>
        <p className="mt-2 text-sm text-[#3a362f]">Losing this link means losing access.</p>
        <div className="mt-4 flex justify-center">
          {src ? <img src={src} alt="Room QR code" className="h-56 w-56" /> : <div>Loading…</div>}
        </div>
        <button
          className="mt-6 w-full rounded-full border-2 border-[#171613] bg-[#171613] px-4 py-2 text-sm font-semibold text-[#f6f0e8]"
          onClick={onClose}
          type="button"
        >
          Close
        </button>
      </div>
    </div>
  );
};

export default QrModal;
