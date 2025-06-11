let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();
let today = new Date().getDate();
let selectedCell = null;

function renderCalendar(month, year) {
    const monthYearElement = document.getElementById("month-year");
    const calendarBody = document.getElementById("calendar-body");

    // Atualiza o título do mês e ano
    monthYearElement.innerText = new Date(year, month).toLocaleDateString("pt-BR", { month: "long", year: "numeric" });

    calendarBody.innerHTML = "";
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    let row = document.createElement("tr");

    // Adiciona células vazias até o primeiro dia do mês
    for (let i = 0; i < firstDay; i++) {
        row.appendChild(document.createElement("td"));
    }

    // Preenche os dias do mês
    for (let i = 1; i <= daysInMonth; i++) {
        let cell = document.createElement("td");
        cell.innerText = i;
        cell.onclick = function () { selectDay(this, i, month, year); };

        // Marca o dia atual como selecionado ao carregar
        if (i === today && month === new Date().getMonth() && year === new Date().getFullYear()) {
            cell.classList.add("selected");
            selectedCell = cell;
            document.getElementById("selected-day").innerText = `Dia ${i}/${String(month + 1).padStart(2, "0")}/${year}`;
        }

        row.appendChild(cell);

        // Quebra a linha ao completar uma semana
        if ((firstDay + i) % 7 === 0) {
            calendarBody.appendChild(row);
            row = document.createElement("tr");
        }
    }

    // Adiciona a última linha ao calendário
    calendarBody.appendChild(row);
}

function changeMonth(step) {
    currentMonth += step;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    } else if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    renderCalendar(currentMonth, currentYear);
}

function selectDay(cell, day, month, year) {
    if (selectedCell) {
        selectedCell.classList.remove("selected");
    }
    selectedCell = cell;
    selectedCell.classList.add("selected");
    document.getElementById("selected-day").innerText = `Dia ${day}/${String(month + 1).padStart(2, "0")}/${year}`;
}

renderCalendar(currentMonth, currentYear);




