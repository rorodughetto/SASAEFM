var calendar;
var Calendar = FullCalendar.Calendar;
var events = [];

// Liste de couleurs pour les événements (couleurs plus douces)
var eventColors = ['#E57373', '#81C784', '#64B5F6', '#FFD54F', '#FF8A65'];

$(function () {
    if (!!scheds) {
        Object.keys(scheds).map((k, index) => { // Utilisez l'index pour obtenir une couleur différente
            var row = scheds[k];
            events.push({
                id: row.id,
                title: row.room_name + " : " + row.author + " pour " + row.deceased_name, // Utilisez "room_name" comme "title"
                start: row.start_datetime,
                end: row.end_datetime,
                backgroundColor: eventColors[index % eventColors.length], // Sélectionne une couleur de la liste
            });
        });
    }
    var date = new Date();
    var d = date.getDate(),
        m = date.getMonth(),
        y = date.getFullYear();
    console.log(events)
    calendar = new Calendar(document.getElementById('calendar'), {
        locale: 'fr',
        headerToolbar: {
            left: 'prev,next today',
            right: 'dayGridMonth,dayGridWeek,list',
            center: 'title',
        },
        themeSystem: 'bootstrap',
        events: events,
        eventClick: function (info) {
            var _details = $('#event-details-modal');
            var id = info.event.id;
            if (!!scheds[id]) {
                _details.find('#author').text(scheds[id].author);
                _details.find('#deceased_name').text(scheds[id].deceased_name);
                _details.find('#room_name').text(scheds[id].room_name);
                _details.find('#start_datetime').text(scheds[id].start_datetime);
                _details.find('#end_datetime').text(scheds[id].end_datetime);
                _details.find('#toilet_and_dressing').text(scheds[id].toilet_and_dressing); // Ajoutez ces lignes
                _details.find('#care').text(scheds[id].care);
                _details.find('#ritual_toilet').text(scheds[id].ritual_toilet);
                _details.find('#technical_room_reservation').text(scheds[id].technical_room_reservation);
                _details.find('#technical_room_reservation_time').text(scheds[id].technical_room_reservation_time);
                _details.find('#edit,#delete').attr('data-id', id);
                _details.modal('show');
            } else {
                alert("Event is undefined");
            }
        },
        eventDidMount: function (info) {
            // Do Something after events mounted
        },
        editable: false
    });

    calendar.render();

    // Form reset listener
    $('#schedule-form').on('reset', function () {
        $(this).find('input:hidden').val('');
        $(this).find('input:visible').first().focus();
    });

    // Delete Button / Deleting an Event
    $('#delete').click(function () {
        var id = $(this).attr('data-id');
        if (!!scheds[id]) {
            var _conf = confirm("Êtes vous sur de vouloir supprimer cette évènement ?");
            if (_conf === true) {
                location.href = "./delete_schedule.php?id=" + id;
            }
        } else {
            alert("Event is undefined");
        }
    });
});
