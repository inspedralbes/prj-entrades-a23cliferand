const BASE_URL = import.meta.env.VITE_API_URL ?? "http://localhost:8000/api";

// Funció per a tot
async function request(endpoint, options = {}) {
  const url = BASE_URL + endpoint;
  
  // Preparem les capçaleres de manera clàssica
  let headers = {
    "Content-Type": "application/json",
    "Accept": "application/json"
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
    body: options.body
  });

  // Si la resposta falla, ho detectem de forma clara
  if (response.ok === false) {
    throw new Error("S'ha produït un error de connexió amb la API o el servidor.");
  }

  // Casos on el servidor no vol retornar un JSON (codi 204 = No Content)
  if (response.status === 204) {
    return null;
  }

  // Retornem directament les dades ja desxifrades de JSON a JavaScript
  const dadesResultants = await response.json();
  return dadesResultants;
}

// Adaptar les dades de les pelicules
export function normalizePelicula(raw) {
  // Gèneres
  const generes =
    typeof raw.generes === "string"
      ? raw.generes
          .split(",")
          .map((g) => g.trim())
          .filter(Boolean)
      : Array.isArray(raw.generes)
        ? raw.generes
        : [];

  // Cartell
  let cartell = "";
  if (raw.cartell_b64 && raw.cartell_mime) {
    cartell = `data:${raw.cartell_mime};base64,${raw.cartell_b64}`;
  } else {
    cartell = raw.cartell ?? "";
  }

  return {
    // identificador = IMDB ID
    id: raw.imdb_id ?? raw.id ?? "",
    titol: raw.titol ?? "",
    titolOriginal: raw.titol_original ?? "",
    sinopsi: raw.sinopsi ?? "",
    poster: cartell,
    backdrop: cartell, // Usem el mateix cartell (Base64 si n'hi ha)
    duracio: raw.duracio ? Number(raw.duracio) : 0,
    any: raw.any ? Number(raw.any) : 0,
    puntuacio: raw.rating ? String(raw.rating) : "—",
    vots: raw.vots ? Number(raw.vots) : 0,
    generes,
    tipus: raw.tipus ?? "",
  };
}

// Adaptar les dades de les sessions
export function normalizeSessio(raw) {
  const dataHora = raw.data_hora ? new Date(raw.data_hora) : null;

  const salaObj = raw.sala ?? {};
  const peliculaObj = raw.pellicula ?? {};

  // Noms dels dies de la setmana i mesos
  const nomsDia = ['Dg', 'Dl', 'Dm', 'Dc', 'Dj', 'Dv', 'Ds'];
  const nomsMes = ['Gen', 'Feb', 'Març', 'Abr', 'Maig', 'Juny', 'Jul', 'Ago', 'Set', 'Oct', 'Nov', 'Des'];

  return {
    id: raw.id,
    peliculaId: raw.pellicula_id ?? peliculaObj.imdb_id ?? "",
    salaId: raw.sala_id,
    tarifaId: raw.tarifa_id,
    dataHora,
    // Dia per agrupar les sessions (format YYYY-MM-DD)
    dia: dataHora ? dataHora.toISOString().split("T")[0] || "" : "",
    esPassat: dataHora
      ? new Date(dataHora).setHours(0, 0, 0, 0) <
        new Date().setHours(0, 0, 0, 0)
      : false,
    hora: dataHora
      ? dataHora.toLocaleTimeString("ca-ES", {
          hour: "2-digit",
          minute: "2-digit",
        })
      : "—",
    // Informació de data per a la tarjeta
    diaSemana: dataHora ? nomsDia[dataHora.getDay()] : "—",
    numDia: dataHora ? dataHora.getDate() : "—",
    mesSessio: dataHora ? nomsMes[dataHora.getMonth()] : "—",
    sala: salaObj.nom ?? `Sala ${raw.sala_id}`,
    capacitat: salaObj.capacitat ?? 0,
    // Dades d'ocupació del servidor
    placesLliures: raw.occupancy?.seients_lliures ?? null,
    seientReservats: raw.occupancy?.seients_reservats ?? 0,
    seientVenuts: raw.occupancy?.seients_venuts ?? 0,
    totalSeients: raw.occupancy?.total_seients ?? 0,
    ocupacioPercent: raw.occupancy?.percentatge_ocupat ?? null,
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
  return request(`/sessions/${id}`, { method: "PUT", body: JSON.stringify(data) });
}

export function deleteSession(id) {
  return request(`/sessions/${id}`, { method: "DELETE" });
}

// Sales
export function getSalesAll() { return request("/sales"); }
export function getSalaById(id) { return request(`/sales/${id}`); }
export function createSala(data) { return request("/sales", { method: "POST", body: JSON.stringify(data) }); }
export function updateSala(id, data) { return request(`/sales/${id}`, { method: "PUT", body: JSON.stringify(data) }); }
export function deleteSala(id) { return request(`/sales/${id}`, { method: "DELETE" }); }

// Reserves
export function getReservesAll() { return request("/reserves"); }
export function getReservesMeves() { return request("/reserves?meves=1"); }
export function getReservaById(id) { return request(`/reserves/${id}`); }
export function createReserva(data) { return request("/reserves", { method: "POST", body: JSON.stringify(data) }); }
export function deleteReserva(id) { return request(`/reserves/${id}`, { method: "DELETE" }); }

// Obté els seients disponibles per a una sessió
export async function getSeientsSessio(sessioId) {
  return request(`/sessions/${sessioId}/seients`);
}

// Crea una reserva d'un sol seient (des del click)
export async function crearReservaSeient(sessioId, seientId, usuariId) {
  return request("/reserves/seient_reservar", {
    method: "POST",
    body: JSON.stringify({
      usuari_id: usuariId,
      sessio_id: sessioId,
      seient_ids: [seientId],
      tipus_client_id: 1
    })
  });
}

// Desocupa (allibera) seients d'una reserva
export async function desocuparSeients(reservaId, seientIds) {
  return request("/reserves/seient_desocupar", {
    method: "POST",
    body: JSON.stringify({
      reserva_id: reservaId,
      seient_ids: seientIds
    })
  });
}

// Obté les reserves de l'usuari per a una sessió específica
export async function lesMevesReserves(usuarioId, sessioId) {
  return request(`/reserves/usuario/${usuarioId}/sessio/${sessioId}`);
}

// Usuaris

export function getUsuarisAll() { return request("/usuaris"); }
export function getUsuariById(id) { return request(`/usuaris/${id}`); }
export function createUsuari(data) { return request("/usuaris", { method: "POST", body: JSON.stringify(data) }); }
export function updateUsuari(id, data) { return request(`/usuaris/${id}`, { method: "PUT", body: JSON.stringify(data) }); }
export function deleteUsuari(id) { return request(`/usuaris/${id}`, { method: "DELETE" }); }

// Tarifes

export function getTarifesAll() { return request("/tarifes"); }
export function getTarifaById(id) { return request(`/tarifes/${id}`); }
export function createTarifa(data) { return request("/tarifes", { method: "POST", body: JSON.stringify(data) }); }
export function updateTarifa(id, data) { return request(`/tarifes/${id}`, { method: "PUT", body: JSON.stringify(data) }); }
export function deleteTarifa(id) { return request(`/tarifes/${id}`, { method: "DELETE" }); }

