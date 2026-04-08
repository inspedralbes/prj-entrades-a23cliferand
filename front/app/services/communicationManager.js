const BASE_URL = import.meta.env.VITE_API_URL;

const NOMS_DIA = ["Dg", "Dl", "Dm", "Dc", "Dj", "Dv", "Ds"];
const NOMS_MES = [
  "Gen",
  "Feb",
  "Març",
  "Abr",
  "Maig",
  "Juny",
  "Jul",
  "Ago",
  "Set",
  "Oct",
  "Nov",
  "Des",
];

// Funció per a tot
async function request(endpoint, options = {}) {
  const url = BASE_URL + endpoint;

  // Preparem les capçaleres de manera clàssica
  let headers = {
    "Content-Type": "application/json",
    Accept: "application/json",
  };

  // Busquem el token per si l'usuari ha fet login
  const token = localStorage.getItem("auth_token");
  if (token !== null) {
    headers["Authorization"] = "Bearer " + token;
  }

  // Fem la petició
  const response = await fetch(url, {
    method: options.method || "GET",
    headers: headers,
    body: options.body,
  });

  // Casos on el servidor no vol retornar un JSON (codi 204 = No Content)
  if (response.status === 204) {
    return null;
  }

  // Retornem directament les dades ja desxifrades de JSON a JavaScript
  const dadesResultants = await response.json();

  // Si la resposta falla, ho detectem de forma clara
  if (response.ok === false) {
    console.error(`${response.status}:`, dadesResultants);
    console.log(
      dadesResultants.error ||
        dadesResultants.message ||
        "S'ha produït un error de connexió amb la API o el servidor.",
    );
  }

  return dadesResultants;
}

// Auth
export function login(email, password) {
  return request("/auth/login", {
    method: "POST",
    body: JSON.stringify({ email, password }),
  });
}

export function register(name, email, password) {
  return request("/auth/register", {
    method: "POST",
    body: JSON.stringify({ name, email, password }),
  });
}

export function logout() {
  return request("/auth/logout", { method: "POST" });
}

export function transferirReservesGuest(guestId) {
  return request("/reserves/transferir-guest", {
    method: "POST",
    body: JSON.stringify({ guest_id: guestId }),
  });
}

// Funcions de normalització per a les dades de la API
export function normalizePelicula(raw) {
  // Normalizar géneros de forma más clara
  const parseGeneres = (generes) => {
    if (typeof generes === "string") {
      return generes
        .split(",")
        .map((g) => g.trim())
        .filter(Boolean);
    }
    return Array.isArray(generes) ? generes : [];
  };

  // Normalizar cartell
  const parseCartell = (b64, mime, fallback) => {
    return b64 && mime ? `data:${mime};base64,${b64}` : (fallback ?? "");
  };

  return {
    id: raw.imdb_id ?? raw.id ?? "",
    titol: raw.titol ?? "",
    titolOriginal: raw.titol_original ?? "",
    sinopsi: raw.sinopsi ?? "",
    poster: parseCartell(raw.cartell_b64, raw.cartell_mime, raw.cartell),
    backdrop: parseCartell(raw.cartell_b64, raw.cartell_mime, raw.cartell),
    duracio: Number(raw.duracio) || 0,
    any: Number(raw.any) || 0,
    puntuacio: String(raw.rating) ?? "—",
    vots: Number(raw.vots) || 0,
    generes: parseGeneres(raw.generes),
    tipus: raw.tipus ?? "",
  };
}

export function normalizeSessio(raw) {
  const dataHora = raw.data_hora ? new Date(raw.data_hora) : null;
  const salaObj = raw.sala ?? {};
  const occupancy = raw.occupancy ?? {};

  let esPassat = false;
  if (dataHora) {
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    esPassat = dataHora < hoy;
  }

  return {
    id: raw.id,
    peliculaId: raw.pellicula_id ?? raw.pellicula?.imdb_id ?? "",
    salaId: raw.sala_id,
    tarifaId: raw.tarifa_id,
    dataHora,
    dia: dataHora ? dataHora.toISOString().split("T")[0] : "",
    esPassat,
    hora: dataHora
      ? dataHora.toLocaleTimeString("ca-ES", {
          hour: "2-digit",
          minute: "2-digit",
        })
      : "—",
    diaSemana: dataHora ? NOMS_DIA[dataHora.getDay()] : "—",
    numDia: dataHora ? dataHora.getDate() : "—",
    mesSessio: dataHora ? NOMS_MES[dataHora.getMonth()] : "—",
    sala: salaObj.nom ?? `Sala ${raw.sala_id}`,
    capacitat: salaObj.capacitat ?? 0,
    placesLliures: occupancy.seients_lliures ?? null,
    seientReservats: occupancy.seients_reservats ?? 0,
    seientVenuts: occupancy.seients_venuts ?? 0,
    totalSeients: occupancy.total_seients ?? 0,
    ocupacioPercent: occupancy.percentatge_ocupat ?? null,
  };
}

// Pel·lícules

// Retorna totes les pel·lícules normalitzades
export async function getPeliculesAll() {
  const data = await request("/pelicules");
  return Array.isArray(data) ? data.map(normalizePelicula) : [];
}

// Retorna una pel·lícula per IMDB ID (string), normalitzada
export async function getPeliculesById(imdbId) {
  const data = await request(`/pelicules/${imdbId}`);
  return normalizePelicula(data);
}

// Sincronitza totes les pel·lícules amb la font externa
export function syncPeliculesAll() {
  return request("/pelicules/sync/all", { method: "POST" });
}

// Sincronitza una pel·lícula concreta per IMDB ID
export function syncPeliculaSingle(imdbId) {
  return request(`/pelicules/sync/${imdbId}`, { method: "POST" });
}

// Sessions

// Retorna totes les sessions normalitzades (inclou relació sala i pellicula)
export async function getSessionsAll() {
  const data = await request("/sessions");
  return Array.isArray(data) ? data.map(normalizeSessio) : [];
}

// Filtra sessions per pel·lícula al client
export async function getSessionsByPelicula(peliculaId) {
  const totes = await getSessionsAll();
  return totes.filter((s) => String(s.peliculaId) === String(peliculaId));
}

// Retorna una sessió per ID, normalitzada
export async function getSessionById(id) {
  const data = await request(`/sessions/${id}`);
  return normalizeSessio(data);
}

export function createSession(data) {
  return request("/sessions", { method: "POST", body: JSON.stringify(data) });
}

export function updateSession(id, data) {
  return request(`/sessions/${id}`, {
    method: "PUT",
    body: JSON.stringify(data),
  });
}

export function deleteSession(id) {
  return request(`/sessions/${id}`, { method: "DELETE" });
}

// Sales
export function getSalesAll() {
  return request("/sales");
}
export function getSalaById(id) {
  return request(`/sales/${id}`);
}
export function createSala(data) {
  return request("/sales", { method: "POST", body: JSON.stringify(data) });
}
export function updateSala(id, data) {
  return request(`/sales/${id}`, { method: "PUT", body: JSON.stringify(data) });
}
export function deleteSala(id) {
  return request(`/sales/${id}`, { method: "DELETE" });
}

// Reserves
export function getReservesAll() {
  return request("/reserves");
}
export function getReservesMeves() {
  return request("/reserves?meves=1");
}
export function getReservaById(id) {
  return request(`/reserves/${id}`);
}
export function createReserva(data) {
  return request("/reserves", { method: "POST", body: JSON.stringify(data) });
}
export function confirmarReservaFinal(data) {
  return request("/reserves/confirmar", {
    method: "POST",
    body: JSON.stringify(data),
  });
}
export function deleteReserva(id) {
  return request(`/reserves/${id}`, { method: "DELETE" });
}

// Obté els seients disponibles per a una sessió
export async function getSeientsSessio(sessioId) {
  return request(`/sessions/${sessioId}/seients`);
}

// Bloqueja temporalment un seient
export async function crearReservaSeient(sessioId, seientId, usuariId) {
  return request("/reserves/seient_reservar", {
    method: "POST",
    body: JSON.stringify({
      usuari_id: usuariId,
      sessio_id: sessioId,
      seient_ids: [seientId],
    }),
  });
}

// Desocupa (allibera) bloquejos temporals
export async function desocuparSeients(sessioId, seientIds) {
  return request("/reserves/seient_desocupar", {
    method: "POST",
    body: JSON.stringify({
      sessio_id: sessioId,
      seient_ids: seientIds,
    }),
  });
}

// Obté les reserves de l'usuari per a una sessió específica
export async function lesMevesReserves(usuarioId, sessioId) {
  return request(`/reserves/usuario/${usuarioId}/sessio/${sessioId}`);
}

// Usuaris

export function getUsuarisAll() {
  return request("/usuaris");
}
export function getUsuariById(id) {
  return request(`/usuaris/${id}`);
}
export function createUsuari(data) {
  return request("/usuaris", { method: "POST", body: JSON.stringify(data) });
}
export function updateUsuari(id, data) {
  return request(`/usuaris/${id}`, {
    method: "PUT",
    body: JSON.stringify(data),
  });
}
export function deleteUsuari(id) {
  return request(`/usuaris/${id}`, { method: "DELETE" });
}

// Tarifes

export function getTarifesAll() {
  return request("/tarifes");
}
export function getTarifaById(id) {
  return request(`/tarifes/${id}`);
}
export function createTarifa(data) {
  return request("/tarifes", { method: "POST", body: JSON.stringify(data) });
}
export function updateTarifa(id, data) {
  return request(`/tarifes/${id}`, {
    method: "PUT",
    body: JSON.stringify(data),
  });
}
export function deleteTarifa(id) {
  return request(`/tarifes/${id}`, { method: "DELETE" });
}
