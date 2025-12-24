const API_BASE = '/api';

let tours = [], clients = [], bookings = [];

document.addEventListener('DOMContentLoaded', () => {
    loadTours('available');
    loadClients();
    loadBookings();
});

function showSection(sectionId) {
    document.querySelectorAll('.section').forEach(s => s.classList.remove('active'));
    document.getElementById(sectionId).classList.add('active');
}

async function apiRequest(endpoint, options = {}) {
    try {
        const response = await fetch(`${API_BASE}${endpoint}`, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        return { error: true, message: 'Ошибка сервера' };
    }
}

async function loadTours(type = 'available') {
    const endpoint = type === 'available' ? '/tours?available=true' : '/tours';
    const data = await apiRequest(endpoint);
    
    if (data.data) {
        tours = data.data;
        renderTours(tours);
    }
}

function renderTours(tours) {
    const container = document.getElementById('tours-list');
    container.innerHTML = tours.map(tour => `
        <div class="card">
            <h3>${tour.name}</h3>
            <p><strong>${tour.country_name}</strong></p>
            <p>${tour.start_date} - ${tour.end_date}</p>
            <p>${tour.description}</p>
            <div class="price">${tour.price}₽</div>
            <div class="available-spots">
                Мест: ${tour.available_spots}/${tour.max_people}
                <span class="${tour.available_spots > 0 ? 'available' : 'sold-out'}">
                    ${tour.available_spots > 0 ? 'Есть места' : 'Закончились'}
                </span>
            </div>
        </div>
    `).join('');
}

async function loadBookings() {
    const data = await apiRequest('/bookings');
    if (data.data) {
        bookings = data.data;
        renderBookings(bookings);
    }
}

function renderBookings(bookings) {
    const container = document.getElementById('bookings-list');
    container.innerHTML = bookings.map(booking => `
        <div class="card">
            <h3>${booking.client_name}</h3>
            <p><strong>${booking.tour_name}</strong></p>
            <p>Дата: ${booking.booking_date}</p>
            <p>Статус: <span class="status ${booking.status}">${booking.status}</span></p>
            <div class="price">${booking.total_price}₽</div>
        </div>
    `).join('');
}

async function loadClients() {
    const data = await apiRequest('/clients');
    if (data.data) {
        clients = data.data;
        renderClients(clients);
        populateSelects();
    }
}

function renderClients(clients) {
    const container = document.getElementById('clients-list');
    container.innerHTML = clients.map(client => `
        <div class="card">
            <h3>${client.full_name}</h3>
            <p>Тел: ${client.phone}</p>
            <p>Email: ${client.email}</p>
        </div>
    `).join('');
}

function populateSelects() {
    document.getElementById('clientSelect').innerHTML = clients.map(c => 
        `<option value="${c.id}">${c.full_name}</option>`
    ).join('');
    document.getElementById('tourSelect').innerHTML = tours.map(t => 
        `<option value="${t.id}" ${t.available_spots > 0 ? '' : 'disabled'}>
            ${t.name} (${t.available_spots}/${t.max_people})
        </option>`
    ).join('');
}

function showBookingForm() {
    document.getElementById('booking-form').style.display = 'flex';
}

function hideBookingForm() {
    document.getElementById('booking-form').style.display = 'none';
}

document.getElementById('bookingForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
        client_id: document.getElementById('clientSelect').value,
        tour_id: document.getElementById('tourSelect').value,
        booking_date: document.getElementById('bookingDate').value,
        status: 'confirmed',
        total_price: document.getElementById('totalPrice').value,
        notes: ''
    };

    const result = await apiRequest('/bookings', {
        method: 'POST',
        body: JSON.stringify(formData)
    });

    if (!result.error) {
        hideBookingForm();
        loadBookings();
        alert('Бронирование создано');
    } else {
        alert('Ошибка: ' + (result.message || 'Попробуйте позже'));
    }
});
