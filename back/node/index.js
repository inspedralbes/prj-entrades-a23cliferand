import express from "express";
import { createServer } from "http";
import { Server } from "socket.io";
import dotenv from "dotenv";
import cors from "cors";

dotenv.config();

const app = express();
app.use(cors());
app.use(express.json());

const PORT = process.env.PORT;
const LARAVEL_API_URL = process.env.LARAVEL_API_URL;

const httpServer = createServer(app);
const io = new Server(httpServer, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"],
    transports: ["websocket", "polling"],
  },
  transports: ["websocket", "polling"],
});

// Connexions al Socket
io.on("connection", (socket) => {
  console.log(`Nou client connectat: ${socket.id}`);

  socket.on("disconnect", () => {
    console.log(`Client desconnectat: ${socket.id}`);
  });
});

// Ruta per els sockets
app.post("/api/broadcast", (req, res) => {
  const { event, session_id, seats } = req.body;

  console.log(`Rebut de Laravel per sessió: ${session_id}`, { event, seats });

  if (event === "seats-reserved") {
    // Emetem a tots els clients quan es reserven seients
    console.log(`Emetent seats-reserved`);
    io.emit("seats-updated", {
      session_id: session_id,
      seats: seats,
      status: "reservat",
    });
    console.log(`Emissió completada!`);

    return res
      .status(200)
      .json({ success: true, message: "Broadcast enviat via Socket.io" });
  }

  if (event === "seats-released") {
    // Emetem a tots els clients quan es liberen seients
    console.log(`Emetent seats-released`);
    io.emit("seats-released", {
      session_id: session_id,
      seats: seats,
      status: "lliure",
    });
    console.log(`Emissió seats-released completada!`);

    return res.status(200).json({
      success: true,
      message: "Broadcast seats-released enviat via Socket.io",
    });
  }

  if (event === "seats-sold") {
    // Emetem a tots els clients quan es venen seients definitivament
    console.log(`Emetent seats-sold`);
    io.emit("seats-updated", {
      session_id: session_id,
      seats: seats,
      status: "venut",
    });
    console.log(`Emissió seats-sold completada!`);

    return res.status(200).json({
      success: true,
      message: "Broadcast seats-sold enviat via Socket.io",
    });
  }

  return res.status(400).json({ success: false, message: "Event desconegut" });
});

// Funció per expirar reserves temporals
async function expirarReservesTemporals() {
  console.log("Executant expirar reserves...");

  try {
    const response = await fetch(`${LARAVEL_API_URL}/reserves/expirar`, {
      method: "POST",
    });

    const data = await response.json();
    console.log("Sexo:", data.session_ids);

    // Emit del resultat que torna Laravel
    io.emit("seat-refresh", {
      message: data.message,
      session_ids: Array.isArray(data.session_ids) ? data.session_ids : [],
    });
  } catch (error) {
    console.error("Error expirant seients:", error.message);
  }
}

// Scheduler per expirar reserves temporals cada 5 minuts
setInterval(expirarReservesTemporals, 5 * 60 * 1000);

httpServer.listen(PORT, () => {
  console.log(`Servidor de Socket.io escoltant al port ${PORT}`);
});
