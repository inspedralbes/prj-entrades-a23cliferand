import { io } from "socket.io-client";

const SOCKET_URL = import.meta.env.VITE_SOCKET_URL;

console.log("[Socket] Intentant connectar a:", SOCKET_URL);

export const socket = io(SOCKET_URL, {
  autoConnect: true,
  reconnection: true,
  reconnectionDelay: 1000,
  reconnectionDelayMax: 5000,
  reconnectionAttempts: 5,
  transports: ["websocket", "polling"],
});

socket.on("connect", () => {
  console.log("Conectat al servidor de WebSockets!");
});

socket.on("disconnect", () => {
  console.log("Desconectat del servidor");
});

socket.on("connect_error", (error) => {
  console.error("Error de connexió:", error);
});
