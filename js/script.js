bookingID = 0;

document.addEventListener('DOMContentLoaded', () => {
    bookingID = 0;

    fillBookingsTable();

    submitBookingEventListener();
});

function fillBookingsTable() {
    const table = document.getElementById('bookings-table');

    fetch('/routes/get-bookings.php')
        .then(res => res.json())
        .then(data => {
            const headers = ['id', 'date', 'pax', 'purpose'];

            const tHead = table.createTHead();
            const headerRow = tHead.insertRow();
            
            let th = null;
            for (let i = 0; i < headers.length; i++) {
                th = document.createElement('th');
                th.textContent = headers[i];
                headerRow.appendChild(th);
            }

            const tBody = table.createTBody();

            let tr = null;
            let td = null;
            let key = '';
            
            if (data.length == 0) {
                tr = tBody.insertRow();
                td = tr.insertCell(0);
                td.setAttribute('id', 'no-booking-found');

                td.textContent = 'There are currently no booking.'
                td.colSpan = 4
            }
            else {
                for (let i = 0; i < data.length; i++) {
                    tr = tBody.insertRow();
    
                    for (let j = 0; j < headers.length; j++) {
                        key = headers[j];
    
                        td = tr.insertCell(j);
                        td.textContent = data[i][key];
                    }
    
                    bookingID++;
                }
            }
            
        });
}

function submitBookingEventListener() {
    document.getElementById('form').addEventListener('submit', (event) => {
        event.preventDefault();

        const message = document.getElementById('message');

        const date = document.getElementById('date').value;
        const pax = document.getElementById('pax').value;
        const purpose = document.getElementById('purpose').value;

        const bookingData = {
            date: date,
            pax: pax,
            purpose: purpose
        };

        let options = {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(bookingData)
        };
        
        fetch('/routes/submit-booking.php', options)
            .then(res => res.json())
            .then(data => {
                if (data['error']) {
                    message.innerHTML = '<p>Error: Unable to connecto to the database.</p>';
                    clearMessage();

                    return;
                }

                if (data['success'] === true) {
                    message.innerHTML = `<h4>Booking Submitted!</h4>
                                            <table>
                                                <tr>
                                                    <td>Date:</td>
                                                    <td>${date}</td>
                                                </tr>
                                                <tr>
                                                    <td>Pax:</td>
                                                    <td>${pax}</td>
                                                </tr>
                                                <tr>
                                                    <td>Purpose:</td>
                                                    <td>${purpose}</td>
                                                </tr>
                                            </table>`

                    const rowData = [bookingID, date, pax, purpose];
                    insertTableRow(rowData); 

                   clearMessage();
                }
                else {
                    message.innerHTML = '<p>Please fill up all the fields.</p>';
                    clearMessage();
                }

                function clearMessage() {
                    setTimeout(() => {
                        message.innerHTML = '';
                    }, 3000);   
                }

            }); 

    });
}

function insertTableRow(data) {
    const table = document.getElementById('bookings-table');
    const tBody = table.tBodies[0];
    
    const tr = tBody.insertRow();
    let td = null;

    for (let i = 0; i < data.length; i++) {
        td = tr.insertCell(i);
        td.textContent = data[i];
    }

    bookingID++;
}