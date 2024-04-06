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
                title: row.author + " pour " + row.deceased_name, // Utilisez "room_name" comme "title"
                start: row.start_time,
                end: row.end_time,
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
            right: 'dayGridWeek,dayGridMonth,list',
            center: 'title',
        },
        themeSystem: 'bootstrap',
        events: events,
        defaultView: 'dayGridMonth',
        eventClick: function (info) {
            var _details = $('#event-details-modal');
            var id = info.event.id;


            var startDatetime = scheds[id].start_time;
            var endDatetime = scheds[id].end_time;
      
            // Convertissez les valeurs de date et d'heure au format jour
            var startDate = new Date(startDatetime);
            var endDate = new Date(endDatetime);
      
            // Obtenez la date au format jour (format AAAA-MM-JJ)
            var startDateString =
              startDate.toISOString().split("T")[0] +
              " " +
              ("0" + startDate.getHours()).slice(-2) +
              ":" +
              ("0" + startDate.getMinutes()).slice(-2);
            var endDateString =
              endDate.toISOString().split("T")[0] +
              " " +
              ("0" + endDate.getHours()).slice(-2) +
              ":" +
              ("0" + endDate.getMinutes()).slice(-2);

            if (!!scheds[id]) {
                _details.find('#author').text(scheds[id].author);
                _details.find('#deceased_name').text(scheds[id].deceased_name);
                _details.find('#start_time').text(startDateString);
                _details.find('#end_time').text(endDateString);
                 _details.find('#tecnical_option').text(scheds[id].tecnical_option);
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
   calendar.changeView('dayGridWeek');
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
                location.href = "./delete_tecnical_schedule.php?id=" + id;
            }
        } else {
            alert("Event is undefined");
        }
    });
});
