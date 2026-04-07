import { io } from "socket.io-client";

const SOCKET_URL = import.meta.env.VITE_SOCKET_URL;

console.log("[Socket] Intentant connectar a:", SOCKET_URL);

export const socket = io(SOCKET_URL, {
  autoConnect: true,
  reconnection: true,
  reconnectionDelay: 1000,
  reconnectionDelayMax: 5000,
  reconnectionAttempts: 5,
  transports: ["websocket", "polling"]
});

socket.on("connect", () => {
  console.log(
    "Conectat al servidor de WebSockets!",
  );
});

socket.on("disconnect", () => {
  console.log("Desconectat del servidor");
});

socket.on("connect_error", (error) => {
  console.error("Error de connexió:", error);
});

socket.on("seats-released", (data) => {
  console.log("Seients alliberats rebuts:", data);
});


export function setupSeientsListeners(sessioId, seients, onSeatsUpdated, onSeatsReleased) {
  // Listener per a seients reservats
  socket.on('seats-updated', (data) => {
    console.log('Hem rebut actualització!', data);
    
    // Validació de sessió
    if (data.session_id == sessioId) {
      data.seats.forEach(seatId => {
        const seientARepintar = seients.find(s => s.id == seatId);
        if (seientARepintar) {
          seientARepintar.estat = data.status || 'reservat';
        }
      });
      onSeatsUpdated?.(data);
    }
  });

  // Listener per a seients alliberats
  socket.on('seats-released', (data) => {
    console.log('Hem rebut alliberació!', data);
    
    // Validació de sessió
    if (data.session_id == sessioId) {
      data.seats.forEach(seatId => {
        const seientARepintar = seients.find(s => s.id == seatId);
        if (seientARepintar) {
          seientARepintar.estat = data.status || 'lliure';
        }
      });
      onSeatsReleased?.(data);
    }
  });
}

/**
 * Elimina els listeners de seients
 */
export function netejarSeientsListeners() {
  socket.off('seats-updated');
  socket.off('seats-released');
}