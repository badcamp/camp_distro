/**
 * @file
 * Contains the definition of the behaviour jsTestBlackWeight.
 */

(function ($, Drupal, drupalSettings) {

  'use strict';

  Drupal.behaviors.scheduler = {
    attach: function (context, settings) {

      function getCsrfToken(callback) {
        jQuery
            .get(Drupal.url('rest/session/token'))
            .done(function (data) {
              var csrfToken = data;
              callback(csrfToken);
            });
      }

      function patchNode(csrfToken, id, node){
        $.ajax({
          url: "/node/" + id + '?_format=json',
          type: 'PATCH',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': csrfToken
          },
          data: JSON.stringify(node),
          success: function(data, textStatus, jQxhr) {
            console.log('success', data, textStatus, jQxhr)
          },
          error: function( jqXhr, textStatus, errorThrown ){
            console.log( errorThrown );
          }
        });
      }

      function runPatch(event) {
        getCsrfToken(function (csrfToken) {
          var newNode = {
            type: [
              { target_id: event.bundle }
            ],
            field_venue: [
              { target_id: event.resourceId }
            ],
            field_date: [
              {
                value: event.start.toISOString(false),
                end_value: event.end.toISOString(false)
              }
            ]
          };
          patchNode(csrfToken, event.id, newNode);
        });
      }

      function runPatchRemove(event) {
        getCsrfToken(function (csrfToken) {
          var newNode = {
            type: [
              { target_id: event.type }
            ],
            field_venue: [],
            field_date: []
          };
          patchNode(csrfToken, event.id, newNode);
        });
      }

      /* initialize the external events
-----------------------------------------------------------------*/

      $('#external-events .fc-event').each(function() {

        // store data so the calendar knows to render an event upon drop
        $(this).data('event', {
          title: $.trim($(this).text()), // use the element's text as the event title
          stick: true, // maintain when user navigates (see docs on the renderEvent method)
          bundle: $(this).data('bundle'),
          entity: $(this).data('entity'),
          duration: '00:15',
          id: $(this).data('nid')
        });

        // make the event draggable using jQuery UI
        $(this).draggable({
          zIndex: 999,
          revert: true,      // will cause the event to go back to its
          revertDuration: 0  //  original position after the drag
        });

      });


      /* initialize the calendar
      -----------------------------------------------------------------*/

      $('#calendar').fullCalendar({
        now: '2018-02-07',
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        editable: true, // enable draggable events
        droppable: true, // this allows things to be dropped onto the calendar
        dropAccept: '#external-events .fc-event',
        eventOverlap: false,
        aspectRatio: 1.8,
        scrollTime: '06:00', // undo default 6am scrollTime
        slotDuration: '00:15:00',
        header: {
          left: 'today prev,next',
          center: 'title',
          right: 'agendaDay,agendaWeek'
        },
        resourceColumns: [
          {
            labelText: 'Venue',
            field: 'title'
          },
          {
            labelText: 'Capacity',
            field: 'capacity'
          }
        ],
        defaultView: 'timelineDay',
        allDaySlot: false,
        groupByResource: true,
        resources: settings.camp_schedule.venues,
        events: settings.camp_schedule.events,
        drop: function(date, jsEvent, ui, resourceId) {
          console.log('drop', [jsEvent, ui, date, date.format(), resourceId]);
          $(this).remove();
        },
        eventReceive: function(event) { // called when a proper external event is dropped
          console.log('eventReceive', event);
          runPatch(event);
        },
        eventDrop: function(event) { // called when an event (already on the calendar) is moved
          console.log('eventDrop', event);
          runPatch(event);
        },
        eventResize: function(event) {
          console.log('eventResize', event);
          runPatch(event);
        },
        eventClick: function(calEvent, jsEvent, view) {

        },
        eventDragStop: function(event,jsEvent) {

          var trashEl = jQuery('#unschedule');
          var ofs = trashEl.offset();

          var x1 = ofs.left;
          var x2 = ofs.left + trashEl.outerWidth(true);
          var y1 = ofs.top;
          var y2 = ofs.top + trashEl.outerHeight(true);

          if (jsEvent.pageX >= x1 && jsEvent.pageX<= x2 &&
              jsEvent.pageY >= y1 && jsEvent.pageY <= y2) {

            var ask = confirm(Drupal.t('Remove "!title" from calendar?', {'!title': event.title}));
            if(ask == true) {
              $('#calendar').fullCalendar('removeEvents', event.id);
              console.log(event);
              runPatchRemove(event);
            }
          }
        }
      });
    }
  };
})(jQuery, Drupal, drupalSettings);