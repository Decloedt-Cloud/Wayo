<div class="card-calendar px-4 py-3">
    <div class="calendar-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <button class="today-btn me-3" onclick="CalendarApp.goToToday()">TODAY</button>
                    <button class="nav-btn me-2" onclick="CalendarApp.previousPeriod()">
                        <i class="mdi mdi-chevron-left" style="font-size: 25px;"></i>
                    </button>
                    <button class="nav-btn me-3" onclick="CalendarApp.nextPeriod()">
                        <i class="mdi mdi-chevron-right" style="font-size: 25px;"></i>
                    </button>
                    <h2 class="month-nav mb-0" id="monthYear"></h2>
                </div>
            </div>
            <div class="col-md-6 text-end">
                <div class="d-flex align-items-center justify-content-end">
                  <select class="school-filter me-3" id="schoolFilter">
                        <option value=""><?php echo get_phrase('All schools'); ?></option>
                    </select>
                    <select class="class-filter me-3" id="classFilter">
                        <option value=""><?php echo get_phrase('All classes'); ?></option>
                    </select>
                    <select class="view-filter me-3" id="viewFilter">
                        <option value="dayGridMonth"><?php echo get_phrase('Month'); ?></option>
                        <option value="timeGridWeek"><?php echo get_phrase('Week'); ?></option>
                        <option value="timeGridDay"><?php echo get_phrase('Day'); ?></option>
                        <option value="listMonth"><?php echo get_phrase('List'); ?></option>
                    </select>
                </div>
            </div>
        </div>
    </div>
    <!-- FullCalendar container -->
    <div id="calendar"></div>
    <div class="modal fade" id="eventEditModal" tabindex="-1" role="dialog" aria-labelledby="eventEditModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventEditModalLabel"><?php echo get_phrase('event_details'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <input type="hidden" id="eventId" name="id">
                  <input type="hidden" id="currentOccurrenceDate" name="current_occurrence_date">
                  <input type="hidden" id="recurrenceType" name="recurrence_type" value="does_not_repeat">
                  <input type="hidden" id="recurrenceEndDate" name="recurrence_end_date">
                  <input type="hidden" id="customRecurrence" name="custom_recurrence">
                    <div id="eventDetailsView" style="display: none;">
                        <div class="mb-2">
                            <h6><?php echo get_phrase('Title'); ?></h6>
                            <p id="eventTitle" class="mb-0"></p>
                        </div>
                        <div class="mb-2">
                            <h6><?php echo get_phrase('Description'); ?></h6>
                            <p id="eventDescriptionView" class="mb-0"></p>
                        </div>
                        <div class="mb-2">
                            <h6><?php echo get_phrase('Community'); ?></h6>
                            <p id="eventSchool" class="mb-0"></p>
                        </div>
                        <div class="mb-2">
                            <h6><?php echo get_phrase('Class'); ?></h6>
                            <p id="eventClass" class="mb-0"></p>
                        </div>
                        <div class="mb-2">
                            <h6><?php echo get_phrase('From'); ?></h6>
                            <p id="eventStart" class="mb-0"></p>
                        </div>
                        <div class="mb-2">
                            <h6><?php echo get_phrase('To'); ?></h6>
                            <p id="eventEnd" class="mb-0"></p>
                        </div>
                        <div class="mb-2" id="eventRecurrenceSection" style="display: none;">
                            <h6><?php echo get_phrase('Recurrence'); ?></h6>
                            <p id="eventRecurrence" class="mb-0"></p>
                        </div>
                          <div><?php echo get_phrase('number of participants'); ?>
                             <span id="participantCount">0</span>
                        </div>
                        <div class="form-group-calendar mt-3 btn-group-1">
                            <button type="button" class="btn join-meeting-btn" id="joinMeetingBtn" style="display: none;"><?php echo get_phrase('Meeting Not Started') ?></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<script>
const csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
let csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

const CalendarApp = {
  calendar: null,
  currentView: 'dayGridMonth',
  selectedSchool: '',
  selectedClass: '',
  isLoading: false,
  selectedDays: [],
  pollingInterval: null,
  currentEventId: null,
  currentMeetingId: null,
  currentOccurrenceDate: null,
  isPolling: false,
  hasActiveMeetings: false,
  activeMeetings: new Set(),
  activeMeetingsPollingInterval: null,
  lastEventId: null,
  lastMeetingId: null,
  lastOccurrenceDate: null,
  lastHasActiveMeetings: false,

  closeAllPopovers() {
  $('[data-bs-popover]').each(function () {
    try {
      const $el = $(this);
      if ($el.data('bs.popover')) {
        $el.popover('dispose');
      }
    } catch (e) {
      console.warn('Error disposing popover:', e);
    }
  });
  // Supprimer tous les éléments résiduels de popovers dans le DOM
  $('.popover').remove();
},

  init() {
  Object.keys(sessionStorage).forEach(key => {
    if (key.startsWith('events_') || key.startsWith('meeting_state_')) {
      sessionStorage.removeItem(key);
    }
  });
  this.loadSchools();
  this.bindGlobalEvents();
  this.setupResizeListener();

  // Add listener for modal show to close popovers
  $('#eventEditModal, #createEventModal, #recurrenceModal').on('show.bs.modal', () => {
    this.closeAllPopovers();
  });

  document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible') {
      if (this.lastEventId && this.lastMeetingId && this.lastOccurrenceDate) {
        this.startPolling(this.lastEventId, this.lastMeetingId, this.lastOccurrenceDate);
      }
      this.checkAndRestoreActiveMeetings();
    } else {
      this.stopPolling();
      this.stopActiveMeetingsPolling();
    }
  });
},

  clearEventCache() {
    Object.keys(sessionStorage).forEach(key => {
        if (key.startsWith('events_')) {
            sessionStorage.removeItem(key);
        }
    });
},

  initCalendar() {
    const calendarEl = document.getElementById('calendar');
    const now = new Date();
    const isMobile = window.innerWidth <= 576;

    this.calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: isMobile ? 'listMonth' : this.currentView,
        headerToolbar: false,
        lazyFetching: true, // Activer le lazy loading
        views: {
            timeGridDay: { type: 'timeGrid', duration: { days: 1 } },
            listMonth: { type: 'list', duration: { months: 1 } }
        },
        events: (info, successCallback, failureCallback) => {
            this.loadEvents(info.startStr, info.endStr, successCallback, failureCallback);
        },
        eventClick: (info) => {
            info.jsEvent.preventDefault();
            const [eventId, occurrenceDate] = info.event.id.split('_');
            this.showEventDetails(eventId || info.event.id, occurrenceDate || info.event.startStr.split('T')[0]);
        },
        eventMouseEnter: (info) => {
            if (window.innerWidth <= 576) return;
            if (!info.el || $('#createEventModal, #eventEditModal, #recurrenceModal').hasClass('show')) {
              return;
            }
            const el = info.el;
            const event = {
                school_name: info.event.extendedProps.school_name,
                class_name: info.event.extendedProps.class_name,
                starting_time: info.event.start ? info.event.start.toTimeString().slice(0, 5) : '',
                ending_time: info.event.end ? info.event.end.toTimeString().slice(0, 5) : '',
                title: info.event.title
            };
            if ($(el).data('bs.popover')) {
                try {
                    $(el).popover('dispose');
                } catch (e) {
                    console.warn('Error disposing popover:', e);
                }
            }
            const popoverContent = this.getPopoverContent(event);
            try {
                $(el).popover({
                    trigger: 'hover',
                    html: true,
                    content: popoverContent,
                    placement: 'auto',
                    container: 'body',
                    boundary: 'viewport',
                    delay: { show: 100, hide: 50 },
                }).popover('show');
            } catch (e) {
                console.warn('Error initializing popover:', e);
            }
        },
        datesSet: (info) => {
          let displayText;
          switch (this.currentView) {
            case 'dayGridMonth':
            case 'listMonth':
              displayText = info.view.calendar.getDate().toLocaleString('default', { month: 'long', year: 'numeric' });
              break;
            case 'timeGridWeek':
              displayText = `Week of ${this.formatDate(info.start)}`;
              break;
            case 'timeGridDay':
              displayText = this.formatDate(info.start);
              break;
            default:
              displayText = info.view.calendar.getDate().toLocaleString('default', { month: 'long', year: 'numeric' });
          }
          $('#monthYear').text(displayText);
          this.checkAndRestoreActiveMeetings();
          this.pollActiveMeetings();
          if (this.selectedSchool) {
            this.loadClassesWithEvents(info.start, info.end);
          }
        },
        eventContent: (arg) => {
            const isExpired = arg.event.end && (new Date() - new Date(arg.event.end) > 24 * 60 * 60 * 1000);
            const isVisio = arg.event.extendedProps.visio == 1;
            const isRunning = arg.event.extendedProps.isRunning || false;
            return {
                html: `
                    <div class="fc-event-main ${isExpired ? 'expired' : ''} ${isRunning ? 'active-meeting' : ''}">
                        ${isVisio ? '<i class="mdi mdi-video"></i>' : ''}
                        <span class="event-title">${this.escapeHtml(arg.event.title)}</span>
                        ${this.currentView === 'timeGridWeek' || this.currentView === 'timeGridDay'
                            ? `<span class="event-time">(${arg.event.start.toTimeString().slice(0, 5)} - ${arg.event.end.toTimeString().slice(0, 5)})</span>`
                            : ''}
                    </div>`
            };
        },
    });

    this.calendar.render();
    this.pollActiveMeetings();
},


  setupResizeListener() {
    $(window).on('resize', () => {
      const isMobile = window.innerWidth <= 576;
      const newView = isMobile ? 'listMonth' : 'dayGridMonth';
      
      if (this.currentView !== newView) {
        this.currentView = newView;
        this.calendar.changeView(newView);
        $('#viewFilter').val(newView); // Synchroniser le sélecteur de vue
      }
    });
  },

  formatDate(date) {
    if (!(date instanceof Date) || isNaN(date)) return '';
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
  },

  loadSchools() {
  $.ajax({
    url: '<?php echo site_url('student/get_student_schools'); ?>',
    type: 'GET',
    data: { [csrfName]: csrfHash },
    success: (response) => {
      try {
        const data = JSON.parse(response);
        if (data.status === 'success') {
          const schoolSelect = $('#schoolFilter');
          schoolSelect.empty();
          schoolSelect.append('<option value=""><?php echo get_phrase("All schools"); ?></option>');
          data.schools.forEach(school => {
            schoolSelect.append(`<option value="${school.id}">${this.escapeHtml(school.name)}</option>`);
          });
          // Auto-select first school if available
          if (data.schools.length > 0) {
            this.selectedSchool = data.schools[0].id;
            schoolSelect.val(this.selectedSchool);
            // Initialize calendar after school is selected
            this.initCalendar();
            // Load classes for the selected school
            const today = new Date();
            const start = new Date(today.getFullYear(), today.getMonth(), 1);
            const end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            this.loadClassesWithEvents(start, end);
            // Start polling active meetings after calendar initialization
            this.pollActiveMeetings();
          } else {
            // Initialize calendar even if no schools are found
            this.initCalendar();
          }
          csrfHash = data.csrf.csrfHash;
        } else {
          this.showNotification('error', data.message);
          // Initialize calendar to avoid breaking UI
          this.initCalendar();
        }
      } catch (e) {
        this.showNotification('error', 'Invalid server response');
        // Initialize calendar to avoid breaking UI
        this.initCalendar();
      }
    },
    error: () => {
      this.showNotification('error', 'Failed to load schools');
      // Initialize calendar to avoid breaking UI
      this.initCalendar();
    }
  });
},

  loadEvents(start, end, successCallback, failureCallback) {
    if (this.isLoading) return;
    this.isLoading = true;

    if (!this.selectedSchool) {
        successCallback([]);
        this.isLoading = false;
        return;
    }

    const cacheKey = `events_${start}_${end}_${this.selectedSchool}_${this.selectedClass}`;
    const cachedEvents = sessionStorage.getItem(cacheKey);

    if (cachedEvents) {
        try {
            const events = JSON.parse(cachedEvents);
            // Preserve isRunning and participant_count from existing events
            const existingEvents = this.calendar ? this.calendar.getEvents() : [];
            const existingEventMap = new Map();
            existingEvents.forEach(event => {
                existingEventMap.set(event.id, {
                    isRunning: event.extendedProps.isRunning,
                    participant_count: event.extendedProps.participant_count,
                    meeting_id: event.extendedProps.meeting_id
                });
            });

            const enrichedEvents = events.map(event => {
                const existing = existingEventMap.get(event.id);
                if (existing) {
                    return {
                        ...event,
                        extendedProps: {
                            ...event.extendedProps,
                            isRunning: existing.isRunning || false,
                            participant_count: existing.participant_count || 0,
                            meeting_id: existing.meeting_id || event.extendedProps.meeting_id
                        }
                    };
                }
                return event;
            });

            if (this.calendar) {
                this.calendar.getEvents().forEach(event => event.remove());
            }
            successCallback(enrichedEvents);
            this.isLoading = false;
            $('#calendar').removeClass('loading');
            return;
        } catch (e) {
            console.warn('Erreur lors de l\'analyse du cache:', e);
        }
    }

    $.ajax({
        url: '<?php echo site_url('student/get_events'); ?>',
        type: 'GET',
        data: {
            school_id: this.selectedSchool,
            start_date: start.split('T')[0],
            end_date: end.split('T')[0],
            class_id: this.selectedClass,
            [csrfName]: csrfHash
        },
        beforeSend: () => { $('#calendar').addClass('loading'); },
        success: (response) => {
            try {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    const events = [];
                    const uniqueEventIds = new Set();
                    // Preserve isRunning and participant_count from existing events
                    const existingEvents = this.calendar ? this.calendar.getEvents() : [];
                    const existingEventMap = new Map();
                    existingEvents.forEach(event => {
                        existingEventMap.set(event.id, {
                            isRunning: event.extendedProps.isRunning,
                            participant_count: event.extendedProps.participant_count,
                            meeting_id: event.extendedProps.meeting_id
                        });
                    });

                    data.data.forEach(event => {
                        if (!event.starting_date || !event.starting_time || !event.ending_time) {
                            console.warn('Skipping invalid event:', event);
                            return;
                        }

                        if (event.occurrences && Object.keys(event.occurrences).length > 0) {
                            Object.keys(event.occurrences).forEach(occurrenceDate => {
                                const uniqueId = `${event.id}_${occurrenceDate}`;
                                if (!uniqueEventIds.has(uniqueId) && occurrenceDate >= start.split('T')[0] && occurrenceDate <= end.split('T')[0]) {
                                    const endDate = event.ending_date || event.starting_date;
                                    const occurrenceEndDateTime = new Date(`${occurrenceDate}T${event.ending_time}`);
                                    const isExpired = occurrenceEndDateTime && (new Date() - occurrenceEndDateTime > 24 * 60 * 60 * 1000);
                                    if (!isExpired) {
                                        const existing = existingEventMap.get(uniqueId);
                                        events.push({
                                            id: uniqueId,
                                            title: event.title,
                                            start: `${occurrenceDate}T${event.starting_time}`,
                                            end: `${endDate}T${event.ending_time}`,
                                            extendedProps: {
                                                description: event.description,
                                                school_id: event.school_id,
                                                class_id: event.class_id,
                                                recurrence_type: event.recurrence_type,
                                                recurrence_end_date: event.recurrence_end_date,
                                                custom_recurrence: event.custom_recurrence,
                                                visio: event.visio,
                                                school_name: event.school_name,
                                                class_name: event.class_name,
                                                is_expired: event.is_expired,
                                                occurrence_date: occurrenceDate,
                                                isRunning: existing ? existing.isRunning : false,
                                                participant_count: existing ? existing.participant_count : 0,
                                                meeting_id: event.occurrences[occurrenceDate]?.meeting_id || (existing ? existing.meeting_id : null)
                                            }
                                        });
                                        uniqueEventIds.add(uniqueId);
                                    }
                                }
                            });
                        } else {
                            const uniqueId = event.id;
                            if (!uniqueEventIds.has(uniqueId)) {
                                const endDate = event.ending_date || event.starting_date;
                                const existing = existingEventMap.get(uniqueId);
                                events.push({
                                    id: uniqueId,
                                    title: event.title,
                                    start: `${event.starting_date}T${event.starting_time}`,
                                    end: `${endDate}T${event.ending_time}`,
                                    extendedProps: {
                                        description: event.description,
                                        school_id: event.school_id,
                                        class_id: event.class_id,
                                        recurrence_type: event.recurrence_type,
                                        recurrence_end_date: event.recurrence_end_date,
                                        custom_recurrence: event.custom_recurrence,
                                        visio: event.visio,
                                        school_name: event.school_name,
                                        class_name: event.class_name,
                                        is_expired: event.is_expired,
                                        occurrence_date: event.starting_date,
                                        isRunning: existing ? existing.isRunning : false,
                                        participant_count: existing ? existing.participant_count : 0,
                                        meeting_id: event.meeting_id || (existing ? existing.meeting_id : null)
                                    }
                                });
                                uniqueEventIds.add(uniqueId);
                            }
                        }
                    });

                    sessionStorage.setItem(cacheKey, JSON.stringify(events));

                    if (this.calendar) {
                        this.calendar.getEvents().forEach(event => event.remove());
                    }
                    successCallback(events);
                    csrfHash = data.csrf.csrfHash;
                } else {
                    this.showNotification('error', data.message || 'Failed to load events');
                    failureCallback();
                }
            } catch (e) {
                console.error('Error parsing response:', e, response);
                this.showNotification('error', 'Invalid server response');
                failureCallback();
            }
        },
        error: (xhr) => {
            console.error('AJAX error:', xhr.status, xhr.statusText);
            this.showNotification('error', xhr.status === 403 ? 'Access denied' : 'Failed to load events');
            failureCallback();
        },
        complete: () => {
            this.isLoading = false;
            $('#calendar').removeClass('loading');
        }
    });
},


  loadClassesWithEvents(start, end) {
  if (!this.selectedSchool) {
    return;
  }

  $.ajax({
    url: '<?php echo site_url('student/get_classes_with_events'); ?>',
    type: 'POST',
    data: {
      school_id: this.selectedSchool,
      start_date: this.formatDate(start),
      end_date: this.formatDate(end),
      [csrfName]: csrfHash
    },
    success: (response) => {
      try {
        const classData = JSON.parse(response);
        if (classData.status === 'success') {
          const classSelect = $('#classFilter');
          classSelect.empty();
          classSelect.append('<option value=""><?php echo get_phrase("All classes"); ?></option>');
          classData.classes.forEach(cls => {
            classSelect.append(`<option value="${cls.class_id}">${this.escapeHtml(cls.name)}</option>`);
          });
          classSelect.val(this.selectedClass || '');
          csrfHash = classData.csrf.csrfHash;
          // Refetch events to ensure calendar displays events for the selected school/class
          this.calendar.refetchEvents();
        } else {
          this.showNotification('error', classData.message);
        }
      } catch (e) {
        this.showNotification('error', 'Invalid server response');
      }
    },
    error: () => {
      this.showNotification('error', 'Failed to load classes');
    }
  });
},

  showNotification(type, message, duration = 3000) {
    Swal.fire({
      toast: true,
      position: 'top-end',
      icon: type,
      title: message,
      showConfirmButton: false,
      timer: duration,
      timerProgressBar: true,
      didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
      }
    });
  },

  getPopoverContent(event) {
    const school = this.escapeHtml(event.school_name || event.title || 'N/A');
    const className = this.escapeHtml(event.class_name || 'N/A');
    const start = event.starting_time ? event.starting_time.slice(0, 5) : '';
    const end = event.ending_time ? event.ending_time.slice(0, 5) : '';
    return `
      <div>
        <strong>School:</strong> ${school}<br>
        <strong>Class:</strong> ${className}<br>
        <strong>Start:</strong> ${start}<br>
        <strong>End:</strong> ${end}
      </div>`;
  },

  showEventDetails(eventId, occurrenceDate) {
    $('#eventId').val(String(eventId));
    $('#currentOccurrenceDate').val(occurrenceDate);
    if (!eventId || !occurrenceDate || !this.selectedSchool) {
        this.showNotification('error', 'No event, occurrence date, or school selected');
        return;
    }

    this.stopPolling();

    $('#joinMeetingBtn').addClass('disabled').prop('disabled', true);
    $('#participantCount').text('0');
    $('#joinMeetingBtn').text('<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', true);

    const today = new Date();
    const startDate = this.formatDate(new Date(today.setFullYear(today.getFullYear() - 1)));
    const endDate = this.formatDate(new Date(today.setFullYear(today.getFullYear() + 2)));

    $.ajax({
        url: '<?php echo site_url('student/get_events'); ?>',
        type: 'GET',
        data: { 
            school_id: this.selectedSchool,
            id: eventId, 
            start_date: startDate, 
            end_date: endDate, 
            [csrfName]: csrfHash 
        },
        success: (response) => {
            try {
                const data = JSON.parse(response);
                if (data.status === 'success' && data.data && data.data.length > 0) {
                    const event = data.data[0];
                    const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
                    const isExpired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);
                    event.occurrence_date = occurrenceDate;
                    event.occurrences = event.occurrences || {};

                    const occurrenceData = event.occurrences[occurrenceDate] || {};
                    const isVisio = event.visio == 1;

                    // Populate view fields
                    $('#eventTitle').text(this.escapeHtml(event.title || ''));
                    $('#eventDescriptionView').text(event.description || 'No description');
                    $('#eventSchool').text(event.school_name || event.title || '');
                    $('#eventClass').text(event.class_name || '');
                    $('#eventStart').text(`${event.starting_time ? event.starting_time.slice(0, 5) : ''}`);
                    $('#eventEnd').text(`${event.ending_time ? event.ending_time.slice(0, 5) : ''}`);
                    $('#eventRecurrenceSection').toggle(event.recurrence_type !== 'does_not_repeat');

                    // Handle recurrence display
                    if (event.recurrence_type !== 'does_not_repeat') {
                        let recurrenceText = event.recurrence_type.charAt(0).toUpperCase() + event.recurrence_type.slice(1);
                        if (event.recurrence_type === 'weekly' && event.custom_recurrence) {
                            try {
                                const days = JSON.parse(event.custom_recurrence);
                                if (Array.isArray(days) && days.length > 0) recurrenceText += ` on ${days.join(', ')}`;
                            } catch (e) {
                                console.error('parse custom_recurrence:', e);
                            }
                        }
                        const endDateText = event.recurrence_end_date
                            ? ` until ${event.recurrence_end_date}`
                            : ` until ${this.formatDate(new Date(new Date(event.starting_date).setFullYear(new Date(event.starting_date).getFullYear() + 1)))}`;
                        $('#eventRecurrence').text(recurrenceText + endDateText);
                    } else {
                        $('#eventRecurrence').text('');
                    }

                    // Handle visio and meeting state
                    $('#eventParticipantsSection').toggle(isVisio);
                    $('#joinMeetingBtn').toggle(isVisio);

                    if (isExpired) {
                        $('#joinMeetingBtn').addClass('disabled').prop('disabled', true);
                        $('#participantCount').text('0');
                        $('#joinMeetingBtn').text('<?php echo get_phrase('Meeting Not Started'); ?>');
                        this.stopPolling();
                    } else if (isVisio) {
                        const meetingId = occurrenceData.meeting_id;
                        const cachedState = meetingId ? this.getCachedMeetingState(meetingId) : null;

                        // Use cached state if available to avoid delay
                        if (cachedState) {
                            this.updateParticipantUI(eventId, cachedState.participantCount, cachedState.isRunning, occurrenceDate);
                            $('#joinMeetingBtn').text(cachedState.isRunning ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', false).removeClass('disabled');
                            if (cachedState.isRunning) {
                                this.hasActiveMeetings = true;
                                this.pollActiveMeetings();
                            }
                            this.startPolling(eventId, meetingId, occurrenceDate);
                        }

                        // Fetch meeting state if no valid cache
                        if (!cachedState || Date.now() - cachedState.timestamp > 5000) {
                            $.ajax({
                                url: '<?php echo site_url('bigbluebutton/meeting_states'); ?>',
                                type: 'POST',
                                data: { meetingIDs: [meetingId], [csrfName]: csrfHash },
                                dataType: 'json',
                                success: (response) => {
                                    const status = response.status ? String(response.status).trim().toLowerCase() : '';
                                    if (status === 'success' && response.data && response.data.length > 0) {
                                        const state = response.data[0];
                                        if (state.status === 'success' && String($('#eventId').val()) === String(eventId) && $('#eventEditModal').hasClass('show')) {
                                            this.cacheMeetingState(meetingId, state.participant_count, state.is_running);
                                            this.updateParticipantUI(eventId, state.participant_count, state.is_running, occurrenceDate);
                                            $('#joinMeetingBtn').text(state.is_running ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', false).removeClass('disabled');
                                            if (state.is_running) {
                                                this.hasActiveMeetings = true;
                                                this.pollActiveMeetings();
                                            }
                                            this.startPolling(eventId, meetingId, occurrenceDate);
                                            csrfHash = response.csrf?.csrfHash || csrfHash;
                                        }
                                    } else {
                                        $('#participantCount').text('0');
                                        $('#joinMeetingBtn').text('<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', false).removeClass('disabled');
                                        this.stopPolling();
                                    }
                                },
                                error: (xhr, status, error) => {
                                    $('#participantCount').text('0');
                                    $('#joinMeetingBtn').text('<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', false).removeClass('disabled');
                                    this.stopPolling();
                                }
                            });
                        }
                    } else {
                        $('#participantCount').text('0');
                        $('#joinMeetingBtn').text('<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', false).removeClass('disabled');
                        this.stopPolling();
                    }

                    $('#eventEditModal').off('hidden.bs.modal').on('hidden.bs.modal', () => {
                        $('#joinMeetingBtn').addClass('disabled').prop('disabled', true);
                        $('#participantCount').text('0');
                        $('#joinMeetingBtn').text('<?php echo get_phrase('Meeting Not Started'); ?>');
                        $('#eventEditModalLabel').text('<?php echo get_phrase("event_details"); ?>');
                        this.stopPolling();
                    });

                    $('#eventDetailsView').show();
                    $('#eventForm').hide();
                    $('#eventEditModal').modal('show');
                    csrfHash = data.csrf.csrfHash;
                } else {
                    this.showNotification('error', data.message || 'Failed to load event');
                }
            } catch (e) {
                this.showNotification('error', 'Invalid server response');
            }
        },
        error: (xhr) => {
            this.showNotification('error', 'Failed to load event');
            this.stopPolling();
        }
    });
},

  cacheMeetingState(meetingId, participantCount, isRunning) {
    const cacheKey = `meeting_state_${meetingId}`;
    
    const parsedParticipantCount = parseInt(participantCount, 10);
    const parsedIsRunning = isRunning === true || isRunning === 'true';
    
    const cacheData = {
        participantCount: isNaN(parsedParticipantCount) ? 0 : parsedParticipantCount,
        isRunning: parsedIsRunning,
        timestamp: Date.now()
    };
    
    if (!parsedIsRunning) {
        // Do not cache non-running meetings to avoid stale data
        sessionStorage.removeItem(cacheKey);
    } else {
        sessionStorage.setItem(cacheKey, JSON.stringify(cacheData));
    }
    return cacheData;
},

  getCachedMeetingState(meetingId) {
    const cacheKey = `meeting_state_${meetingId}`;
    const cached = sessionStorage.getItem(cacheKey);
    if (cached) {
      const cacheData = JSON.parse(cached);
      if (Date.now() - cacheData.timestamp < 5000) {
        return cacheData;
      } else {
        sessionStorage.removeItem(cacheKey);
      }
    }
    return null;
  },

  startPolling(eventId, meetingId, occurrenceDate) {
    if (document.visibilityState !== 'visible') {
        return;
    }

    this.stopPolling();
    this.currentEventId = eventId;
    this.currentMeetingId = meetingId;
    this.currentOccurrenceDate = occurrenceDate;


    this.pollingInterval = setInterval(() => {
        if (document.visibilityState !== 'visible') {
            this.stopPolling();
            return;
        }

        const cachedState = this.getCachedMeetingState(meetingId);
        if (cachedState) {
            const modalEventId = $('#eventId').length > 0 ? String($('#eventId').val()) : null;
            const modalOccurrenceDate = $('#currentOccurrenceDate').length > 0 ? $('#currentOccurrenceDate').val() : null;
            if (modalEventId && modalOccurrenceDate && modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                this.updateParticipantUI(eventId, cachedState.participantCount, cachedState.isRunning, occurrenceDate);
                $('#joinMeetingBtn').text(cachedState.isRunning ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', false).removeClass('disabled');
            } else {
            }
            return;
        }

        $.ajax({
            url: '<?php echo site_url('bigbluebutton/meeting_states'); ?>',
            type: 'POST',
            data: { meetingIDs: [meetingId], [csrfName]: csrfHash },
            dataType: 'json',
            success: (response) => {
                const status = response.status ? String(response.status).trim().toLowerCase() : '';
                if (status === 'success' && response.data && response.data.length > 0) {
                    const state = response.data[0];
                    if (state.status === 'success') {
                        const modalEventId = $('#eventId').length > 0 ? String($('#eventId').val()) : null;
                        const modalOccurrenceDate = $('#currentOccurrenceDate').length > 0 ? $('#currentOccurrenceDate').val() : null;
                        if (modalEventId && modalOccurrenceDate && modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                            this.cacheMeetingState(meetingId, state.participant_count, state.is_running);
                            this.updateParticipantUI(eventId, state.participant_count, state.is_running, occurrenceDate);
                            $('#joinMeetingBtn').text(state.is_running ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Meeting Not Started'); ?>');
                            csrfHash = response.csrf?.csrfHash || csrfHash;
                        } else {
                        }
                    } else {
                        const modalEventId = $('#eventId').length > 0 ? String($('#eventId').val()) : null;
                        const modalOccurrenceDate = $('#currentOccurrenceDate').length > 0 ? $('#currentOccurrenceDate').val() : null;
                        if (modalEventId && modalOccurrenceDate && modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                            $('#participantCount').text('0');
                            $('#joinMeetingBtn').text('<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', false);
                        }
                        this.stopPolling();
                    }
                } else {
                    const modalEventId = $('#eventId').length > 0 ? String($('#eventId').val()) : null;
                    const modalOccurrenceDate = $('#currentOccurrenceDate').length > 0 ? $('#currentOccurrenceDate').val() : null;
                    if (modalEventId && modalOccurrenceDate && modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                        $('#participantCount').text('0');
                        $('#joinMeetingBtn').text('<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', false);
                    }
                    this.stopPolling();
                }
            },
            error: (xhr, status, error) => {
                const modalEventId = $('#eventId').length > 0 ? String($('#eventId').val()) : null;
                const modalOccurrenceDate = $('#currentOccurrenceDate').length > 0 ? $('#currentOccurrenceDate').val() : null;
                if (modalEventId && modalOccurrenceDate && modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                    $('#participantCount').text('0');
                    $('#joinMeetingBtn').text('<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', false);
                }
                this.stopPolling();
            }
        });
    }, 5000);
},

  stopPolling() {
    if (this.pollingInterval) {
        this.lastEventId = this.currentEventId;
        this.lastMeetingId = this.currentMeetingId;
        this.lastOccurrenceDate = this.currentOccurrenceDate;
        clearInterval(this.pollingInterval);
        this.pollingInterval = null;
        this.currentEventId = null;
        this.currentMeetingId = null;
        this.currentOccurrenceDate = null;
        if (this.hasActiveMeetings && document.visibilityState === 'visible') {
            this.pollActiveMeetings();
        }
    }
  },

  updateParticipantUI(eventId, participantCount, isRunning, occurrenceDate) {
  this.closeAllPopovers(); // Fermer tous les popovers avant la mise à jour

  const modalEventId = String($('#eventId').val());
  const modalOccurrenceDate = $('#currentOccurrenceDate').val();
  const uniqueEventId = occurrenceDate ? `${eventId}_${occurrenceDate}` : eventId;

  // Mise à jour de l'UI du modal si ouvert
  if (modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
    $('#participantCount').text(participantCount || 0);
    $('#joinMeetingBtn').text(isRunning ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', !isRunning);
  }

  // Mise à jour de l'événement dans le calendrier
  const event = this.calendar.getEventById(uniqueEventId);
  if (event) {
    event.setExtendedProp('isRunning', isRunning);
    event.setExtendedProp('participant_count', participantCount);
    const eventData = {
      id: event.id,
      title: event.title,
      start: event.start,
      end: event.end,
      extendedProps: { ...event.extendedProps, isRunning, participant_count: participantCount }
    };
    event.remove();
    this.calendar.addEvent(eventData);

    // Mise à jour du Set des réunions actives
    if (isRunning) {
      this.activeMeetings.add(JSON.stringify({ meetingId: event.extendedProps.meeting_id, eventId, occurrenceDate }));
    } else {
      this.activeMeetings.delete(JSON.stringify({ meetingId: event.extendedProps.meeting_id, eventId, occurrenceDate }));
      sessionStorage.removeItem(`meeting_state_${event.extendedProps.meeting_id}`);
    }
  }

  // Mise à jour des autres occurrences
  const today = new Date();
  const startDate = this.formatDate(new Date(today.setFullYear(today.getFullYear() - 1)));
  const endDate = this.formatDate(new Date(today.setFullYear(today.getFullYear() + 2)));
  $.ajax({
    url: '<?php echo site_url('student/get_events'); ?>',
    type: 'GET',
    data: { 
      id: eventId, 
      start_date: startDate, 
      end_date: endDate, 
      school_id: this.selectedSchool,
      [csrfName]: csrfHash 
    },
    success: (response) => {
      try {
        const data = JSON.parse(response);
        if (data.status === 'success' && data.data && data.data.length > 0) {
          this.closeAllPopovers(); // Fermer les popovers avant de mettre à jour les autres occurrences
          const eventData = data.data[0];
          if (eventData.occurrences) {
            Object.keys(eventData.occurrences).forEach(date => {
              if (date !== occurrenceDate) {
                const otherUniqueEventId = `${eventId}_${date}`;
                const otherEvent = this.calendar.getEventById(otherUniqueEventId);
                if (otherEvent) {
                  const meetingId = eventData.occurrences[date]?.meeting_id;
                  if (meetingId) {
                    const cachedState = this.getCachedMeetingState(meetingId);
                    const isOtherRunning = cachedState ? cachedState.isRunning : false;
                    const otherParticipantCount = cachedState ? cachedState.participantCount : 0;
                    otherEvent.setExtendedProp('isRunning', isOtherRunning);
                    otherEvent.setExtendedProp('participant_count', otherParticipantCount);
                    const otherEventData = {
                      id: otherEvent.id,
                      title: otherEvent.title,
                      start: otherEvent.start,
                      end: otherEvent.end,
                      extendedProps: { ...otherEvent.extendedProps, isRunning: isOtherRunning, participant_count: otherParticipantCount }
                    };
                    otherEvent.remove();
                    this.calendar.addEvent(otherEventData);
                    if (isOtherRunning) {
                      this.activeMeetings.add(JSON.stringify({ meetingId, eventId, occurrenceDate: date }));
                    } else {
                      this.activeMeetings.delete(JSON.stringify({ meetingId, eventId, occurrenceDate: date }));
                      sessionStorage.removeItem(`meeting_state_${meetingId}`);
                    }
                  } else {
                    otherEvent.setExtendedProp('isRunning', false);
                    otherEvent.setExtendedProp('participant_count', 0);
                    const otherEventData = {
                      id: otherEvent.id,
                      title: otherEvent.title,
                      start: otherEvent.start,
                      end: otherEvent.end,
                      extendedProps: { ...otherEvent.extendedProps, isRunning: false, participant_count: 0 }
                    };
                    otherEvent.remove();
                    this.calendar.addEvent(otherEventData);
                    this.activeMeetings.delete(JSON.stringify({ meetingId, eventId, occurrenceDate: date }));
                  }
                }
              }
            });
          }
          csrfHash = data.csrf.csrfHash;
        } 
      } catch (e) {
        console.error('updateParticipantUI - Error parsing get_events:', e, response);
      }
    },
    error: (xhr) => {
      console.error('updateParticipantUI - AJAX error:', xhr.status, xhr.statusText, 'Response:', xhr.responseText);
    }
  });
},

 startMeeting() {
    const eventId = String($('#eventId').val());
    const occurrenceDate = $('#currentOccurrenceDate').val();
    
    if (!eventId || !occurrenceDate) {
        this.showNotification('error', 'No event or occurrence date selected');
        return;
    }

    if (!this.selectedSchool) {
        this.showNotification('error', 'No school selected');
        return;
    }

    this.stopPolling();
    this.currentMeetingId = null;
    this.currentEventId = null;
    this.currentOccurrenceDate = null;

    const today = new Date();
    const startDate = this.formatDate(new Date(today.setFullYear(today.getFullYear() - 1)));
    const endDate = this.formatDate(new Date(today.setFullYear(today.getFullYear() + 2)));

    $.ajax({
        url: '<?php echo site_url('student/get_events'); ?>',
        type: 'GET',
        data: { 
            id: eventId, 
            start_date: startDate, 
            end_date: endDate, 
            school_id: this.selectedSchool, // Ensure school_id is included
            [csrfName]: csrfHash 
        },
        async: true,
        success: (response) => {
            try {
                const data = JSON.parse(response);
                if (data.status === 'success' && data.data && data.data.length > 0) {
                    const event = data.data[0];
                    const occurrenceData = event.occurrences?.[occurrenceDate] || {};
                    const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
                    const isExpired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);

                    if (isExpired) {
                        this.showNotification('error', 'Event is expired');
                        return;
                    }
                    if (event.visio != 1) {
                        this.showNotification('error', 'This event does not support video conferencing');
                        return;
                    }

                    const buttonText = $('#joinMeetingBtn').text();

                    if (buttonText === '<?php echo get_phrase('Meeting Not Started'); ?>') {
                        $.ajax({
                            url: '<?php echo site_url('student/start_meeting'); ?>',
                            type: 'POST',
                            data: { 
                                event_id: eventId, 
                                occurrence_date: occurrenceDate, 
                                school_id: this.selectedSchool, // Add school_id here
                                [csrfName]: csrfHash 
                            },
                            success: (response) => {
                                try {
                                    const data = JSON.parse(response);
                                    csrfHash = data.csrf.csrfHash;

                                    if (data.status === 'success' && data.meeting_id && data.appointment_id) {
                                        this.cacheMeetingState(data.meeting_id, data.participant_count, data.is_running);
                                        if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                            this.updateParticipantUI(eventId, data.participant_count, data.is_running, occurrenceDate);
                                            $('#joinMeetingBtn').text(data.is_running ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', false);
                                        }

                                        const uniqueEventId = occurrenceDate ? `${eventId}_${occurrenceDate}` : eventId;
                                        const calendarEvent = this.calendar.getEventById(uniqueEventId);
                                        if (calendarEvent) {
                                            calendarEvent.setExtendedProp('isRunning', data.is_running);
                                            calendarEvent.setExtendedProp('participant_count', data.participant_count);
                                            calendarEvent.setExtendedProp('meeting_id', data.meeting_id);
                                            const eventData = {
                                                id: calendarEvent.id,
                                                title: calendarEvent.title,
                                                start: calendarEvent.start,
                                                end: calendarEvent.end,
                                                extendedProps: {
                                                    ...calendarEvent.extendedProps,
                                                    isRunning: data.is_running,
                                                    participant_count: data.participant_count,
                                                    meeting_id: data.meeting_id
                                                }
                                            };
                                            calendarEvent.remove();
                                            this.calendar.addEvent(eventData);
                                        }

                                        this.startPolling(eventId, data.meeting_id, occurrenceDate);

                                        if (data.is_running && !this.activeMeetingsPollingInterval) {
                                            this.hasActiveMeetings = true;
                                            setTimeout(() => {
                                                if (document.visibilityState === 'visible') {
                                                    this.pollActiveMeetings();
                                                }
                                            }, 3000);
                                        }

                                        const joinUrl = data.join_url || '<?php echo site_url('bigbluebutton/join_meeting'); ?>/' + encodeURIComponent(data.meeting_id);
                                        const newWindow = window.open(joinUrl, '_blank');
                                        if (!newWindow || newWindow.closed || typeof newWindow.closed === 'undefined') {
                                            this.showNotification('warning', 'Unable to open meeting. Please allow pop-ups for this site or click <a href="' + joinUrl + '" target="_blank">here</a> to join.', 5000);
                                        } else {
                                            this.showNotification('success', 'Starting meeting...');
                                        }
                                    } else {
                                        this.showNotification('error', data.message || 'Failed to Meeting Not Started');
                                    }
                                } catch (e) {
                                    console.error('start_meeting parse error:', e, response);
                                    this.showNotification('error', 'Invalid server response');
                                }
                            },
                            error: (xhr) => {
                                console.error('start_meeting AJAX error:', xhr.status, xhr.statusText);
                                this.showNotification('error', 'Error starting meeting. Please check server connectivity.');
                            }
                        });
                    } else if (buttonText === '<?php echo get_phrase('Join Meeting'); ?>') {
                        if (!occurrenceData.meeting_id) {
                            this.showNotification('error', 'No meeting ID available for joining');
                            return;
                        }

                        $.ajax({
                            url: '<?php echo site_url('bigbluebutton/meeting_states'); ?>',
                            type: 'POST',
                            data: { 
                                meetingIDs: [occurrenceData.meeting_id], 
                                school_id: this.selectedSchool, // Add school_id here
                                [csrfName]: csrfHash 
                            },
                            dataType: 'json',
                            success: (response) => {
                                const status = response.status ? String(response.status).trim().toLowerCase() : '';
                                if (status === 'success' && response.data && response.data.length > 0) {
                                    const state = response.data[0];
                                    if (state.status === 'success') {
                                        this.cacheMeetingState(occurrenceData.meeting_id, state.participant_count, state.is_running);
                                        if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                            this.updateParticipantUI(eventId, state.participant_count, state.is_running, occurrenceDate);
                                            $('#joinMeetingBtn').text(state.is_running ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', false);
                                        }
                                        const uniqueEventId = occurrenceDate ? `${eventId}_${occurrenceDate}` : eventId;
                                        const calendarEvent = this.calendar.getEventById(uniqueEventId);
                                        if (calendarEvent) {
                                            calendarEvent.setExtendedProp('isRunning', state.is_running);
                                            calendarEvent.setExtendedProp('participant_count', state.participant_count);
                                            const eventData = {
                                                id: calendarEvent.id,
                                                title: calendarEvent.title,
                                                start: calendarEvent.start,
                                                end: calendarEvent.end,
                                                extendedProps: {
                                                    ...calendarEvent.extendedProps,
                                                    isRunning: state.is_running,
                                                    participant_count: state.participant_count
                                                }
                                            };
                                            calendarEvent.remove();
                                            this.calendar.addEvent(eventData);
                                        }
                                        if (state.is_running && !this.activeMeetingsPollingInterval) {
                                            this.hasActiveMeetings = true;
                                            this.pollActiveMeetings();
                                        }
                                        this.startPolling(eventId, occurrenceData.meeting_id, occurrenceDate);
                                    } else {
                                        this.showNotification('error', state.message || 'Meeting is not active');
                                        return;
                                    }
                                } else {
                                    this.showNotification('error', 'Failed to verify meeting state');
                                    return;
                                }
                            },
                            error: (xhr, status, error) => {
                                this.showNotification('error', 'Failed to verify meeting state');
                                return;
                            }
                        });

                        const joinUrl = '<?php echo site_url('bigbluebutton/join_meeting'); ?>/' + encodeURIComponent(occurrenceData.meeting_id);
                        const newWindow = window.open(joinUrl, '_blank');
                        if (!newWindow || newWindow.closed || typeof newWindow.closed === 'undefined') {
                            this.showNotification('warning', 'Unable to open meeting. Please allow pop-ups for this site or click <a href="' + joinUrl + '" target="_blank">here</a> to join.', 5000);
                        } else {
                            this.showNotification('success', 'Joining meeting...');
                            this.startPolling(eventId, occurrenceData.meeting_id, occurrenceDate);
                        }
                    }
                    csrfHash = data.csrf.csrfHash;
                } else {
                    this.showNotification('error', data.message || 'Failed to load event');
                }
            } catch (e) {
                this.showNotification('error', 'Invalid server response');
            }
        },
        error: (xhr) => {
            console.error('AJAX error fetching event', xhr.status, xhr.statusText);
            this.showNotification('error', 'Failed to load event');
        }
    });
},

  pollActiveMeetings() {
  if (this.isPolling || document.visibilityState !== 'visible') return;
  this.isPolling = true;

  const poll = () => {
    if (document.visibilityState !== 'visible') {
      this.stopActiveMeetingsPolling();
      this.isPolling = false;
      return;
    }

    const lastFetchTime = sessionStorage.getItem('lastActiveMeetingsFetch');
    const now = Date.now();
    if (lastFetchTime && now - parseInt(lastFetchTime) < 10000) {
      this.isPolling = false;
      return;
    }

    const view = this.calendar.view;
    const startDate = this.formatDate(view.activeStart);
    const endDate = this.formatDate(view.activeEnd);

    $.ajax({
      url: '<?php echo site_url('student/get_events'); ?>',
      type: 'GET',
      data: { 
        start_date: startDate, 
        end_date: endDate, 
        visio: 1, 
        school_id: this.selectedSchool,
        [csrfName]: csrfHash 
      },
      success: (response) => {
        try {
          const data = JSON.parse(response);
          if (data.status === 'success' && data.data) {
            this.closeAllPopovers(); // Fermer tous les popovers avant la mise à jour
            const meetingIds = new Set();
            const currentActiveMeetings = new Set();
            data.data.forEach(event => {
              const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
              const isExpired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);
              if (!isExpired && event.visio == 1) {
                if (event.occurrences && Object.keys(event.occurrences).length > 0) {
                  Object.keys(event.occurrences).forEach(occurrenceDate => {
                    const meetingId = event.occurrences[occurrenceDate]?.meeting_id;
                    if (meetingId && occurrenceDate >= startDate && occurrenceDate <= endDate) {
                      const occurrenceEndDateTime = new Date(`${occurrenceDate}T${event.ending_time}`);
                      const isOccurrenceExpired = occurrenceEndDateTime && (new Date() - occurrenceEndDateTime > 24 * 60 * 60 * 1000);
                      if (!isOccurrenceExpired) {
                        meetingIds.add(JSON.stringify({ meetingId, eventId: event.id, occurrenceDate }));
                      }
                    }
                  });
                } else if (event.meeting_id && event.starting_date >= startDate && event.starting_date <= endDate) {
                  meetingIds.add(JSON.stringify({ meetingId: event.meeting_id, eventId: event.id, occurrenceDate: event.starting_date }));
                }
              }
            });

            const meetingIdsArray = Array.from(meetingIds).map(item => JSON.parse(item));

            if (meetingIdsArray.length === 0) {
              this.closeAllPopovers(); // Fermer les popovers si aucune réunion active
              this.activeMeetings.forEach(item => {
                const { meetingId, eventId, occurrenceDate } = JSON.parse(item);
                const uniqueEventId = occurrenceDate ? `${eventId}_${occurrenceDate}` : eventId;
                const event = this.calendar.getEventById(uniqueEventId);
                if (event) {
                  event.setExtendedProp('isRunning', false);
                  event.setExtendedProp('participant_count', 0);
                  sessionStorage.removeItem(`meeting_state_${meetingId}`);
                  const eventData = {
                    id: event.id,
                    title: event.title,
                    start: event.start,
                    end: event.end,
                    extendedProps: { ...event.extendedProps, isRunning: false, participant_count: 0 }
                  };
                  event.remove();
                  this.calendar.addEvent(eventData);
                }
              });
              this.activeMeetings.clear();
              this.hasActiveMeetings = false;
              this.isPolling = false;
              this.stopActiveMeetingsPolling();
              return;
            }

            const cachedResults = [];
            const meetingIdsToFetch = [];
            meetingIdsArray.forEach(({ meetingId, eventId, occurrenceDate }) => {
              const cachedState = this.getCachedMeetingState(meetingId);
              if (cachedState && cachedState.isRunning && Date.now() - cachedState.timestamp < 15000) {
                cachedResults.push({ meetingId, eventId, occurrenceDate, ...cachedState });
              } else {
                meetingIdsToFetch.push(meetingId);
              }
            });

            // Nettoyer les réunions obsolètes
            this.activeMeetings.forEach(item => {
              const parsedItem = JSON.parse(item);
              if (!meetingIdsArray.some(m => m.meetingId === parsedItem.meetingId && m.eventId === parsedItem.eventId && m.occurrenceDate === parsedItem.occurrenceDate)) {
                const { meetingId, eventId, occurrenceDate } = parsedItem;
                const uniqueEventId = occurrenceDate ? `${eventId}_${occurrenceDate}` : eventId;
                const event = this.calendar.getEventById(uniqueEventId);
                if (event) {
                  event.setExtendedProp('isRunning', false);
                  event.setExtendedProp('participant_count', 0);
                  sessionStorage.removeItem(`meeting_state_${meetingId}`);
                  const eventData = {
                    id: event.id,
                    title: event.title,
                    start: event.start,
                    end: event.end,
                    extendedProps: { ...event.extendedProps, isRunning: false, participant_count: 0 }
                  };
                  event.remove();
                  this.calendar.addEvent(eventData);
                }
                this.activeMeetings.delete(item);
              }
            });

            if (meetingIdsToFetch.length === 0) {
              let activeMeetingsFound = false;
              cachedResults.forEach(({ meetingId, eventId, occurrenceDate, participantCount, isRunning }) => {
                const uniqueEventId = occurrenceDate ? `${eventId}_${occurrenceDate}` : eventId;
                const event = this.calendar.getEventById(uniqueEventId);
                if (event) {
                  event.setExtendedProp('isRunning', isRunning);
                  event.setExtendedProp('participant_count', participantCount);
                  const eventData = {
                    id: event.id,
                    title: event.title,
                    start: event.start,
                    end: event.end,
                    extendedProps: { ...event.extendedProps, isRunning, participant_count: participantCount }
                  };
                  event.remove();
                  this.calendar.addEvent(eventData);
                  if (isRunning) {
                    activeMeetingsFound = true;
                    this.activeMeetings.add(JSON.stringify({ meetingId, eventId, occurrenceDate }));
                    const modalEventId = String($('#eventId').val());
                    const modalOccurrenceDate = $('#currentOccurrenceDate').val();
                    if (modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                      $('#participantCount').text(participantCount || 0);
                      $('#joinMeetingBtn').text(isRunning ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', !isRunning);
                    }
                  }
                }
              });
              this.hasActiveMeetings = activeMeetingsFound;
              this.isPolling = false;
              sessionStorage.setItem('lastActiveMeetingsFetch', now.toString());
              if (!activeMeetingsFound) {
                this.stopActiveMeetingsPolling();
                this.activeMeetings.clear();
              }
              return;
            }

            $.ajax({
              url: '<?php echo site_url('bigbluebutton/meeting_states'); ?>',
              type: 'POST',
              data: { meetingIDs: meetingIdsToFetch, [csrfName]: csrfHash },
              dataType: 'json',
              success: (response) => {
                if (response.status === 'success' && response.data) {
                  this.closeAllPopovers(); // Fermer les popovers avant de traiter les réponses
                  let activeMeetingsFound = false;
                  response.data.forEach(state => {
                    const matching = meetingIdsArray.find(item => item.meetingId === state.meeting_id);
                    if (!matching) return;

                    const { eventId, occurrenceDate } = matching;
                    const uniqueEventId = occurrenceDate ? `${eventId}_${occurrenceDate}` : eventId;
                    const event = this.calendar.getEventById(uniqueEventId);
                    if (event) {
                      if (state.status === 'success') {
                        this.cacheMeetingState(state.meeting_id, state.participant_count, state.is_running);
                        event.setExtendedProp('isRunning', state.is_running);
                        event.setExtendedProp('participant_count', state.participant_count);
                        event.setExtendedProp('meeting_id', state.meeting_id);
                        const eventData = {
                          id: event.id,
                          title: event.title,
                          start: event.start,
                          end: event.end,
                          extendedProps: { ...event.extendedProps, isRunning: state.is_running, participant_count: state.participant_count, meeting_id: state.meeting_id }
                        };
                        event.remove();
                        this.calendar.addEvent(eventData);
                        if (state.is_running) {
                          activeMeetingsFound = true;
                          this.activeMeetings.add(JSON.stringify({ meetingId: state.meeting_id, eventId, occurrenceDate }));
                          const modalEventId = String($('#eventId').val());
                          const modalOccurrenceDate = $('#currentOccurrenceDate').val();
                          if (modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                            $('#participantCount').text(state.participant_count || 0);
                            $('#joinMeetingBtn').text(state.is_running ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', !state.is_running);
                          }
                        } else {
                          this.activeMeetings.delete(JSON.stringify({ meetingId: state.meeting_id, eventId, occurrenceDate }));
                          sessionStorage.removeItem(`meeting_state_${state.meeting_id}`);
                        }
                      } else {
                        event.setExtendedProp('isRunning', false);
                        event.setExtendedProp('participant_count', 0);
                        const eventData = {
                          id: event.id,
                          title: event.title,
                          start: event.start,
                          end: event.end,
                          extendedProps: { ...event.extendedProps, isRunning: false, participant_count: 0 }
                        };
                        event.remove();
                        this.calendar.addEvent(eventData);
                        this.activeMeetings.delete(JSON.stringify({ meetingId: state.meeting_id, eventId, occurrenceDate }));
                        sessionStorage.removeItem(`meeting_state_${state.meeting_id}`);
                      }
                    }
                  });

                  cachedResults.forEach(({ meetingId, eventId, occurrenceDate, participantCount, isRunning }) => {
                    const uniqueEventId = occurrenceDate ? `${eventId}_${occurrenceDate}` : eventId;
                    const event = this.calendar.getEventById(uniqueEventId);
                    if (event) {
                      event.setExtendedProp('isRunning', isRunning);
                      event.setExtendedProp('participant_count', participantCount);
                      const eventData = {
                        id: event.id,
                        title: event.title,
                        start: event.start,
                        end: event.end,
                        extendedProps: { ...event.extendedProps, isRunning, participant_count: participantCount }
                      };
                      event.remove();
                      this.calendar.addEvent(eventData);
                      if (isRunning) {
                        activeMeetingsFound = true;
                        this.activeMeetings.add(JSON.stringify({ meetingId, eventId, occurrenceDate }));
                        const modalEventId = String($('#eventId').val());
                        const modalOccurrenceDate = $('#currentOccurrenceDate').val();
                        if (modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                          $('#participantCount').text(participantCount || 0);
                          $('#joinMeetingBtn').text(isRunning ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', !isRunning);
                        }
                      }
                    }
                  });

                  this.hasActiveMeetings = activeMeetingsFound;
                  this.isPolling = false;
                  csrfHash = response.csrf?.csrfHash || csrfHash;
                  sessionStorage.setItem('lastActiveMeetingsFetch', now.toString());

                  if (!activeMeetingsFound) {
                    this.stopActiveMeetingsPolling();
                    this.activeMeetings.clear();
                  }
                } else {
                  this.closeAllPopovers(); // Fermer les popovers si aucune donnée de réunion
                  this.activeMeetings.forEach(item => {
                    const { meetingId, eventId, occurrenceDate } = JSON.parse(item);
                    const uniqueEventId = occurrenceDate ? `${eventId}_${occurrenceDate}` : eventId;
                    const event = this.calendar.getEventById(uniqueEventId);
                    if (event) {
                      event.setExtendedProp('isRunning', false);
                      event.setExtendedProp('participant_count', 0);
                      sessionStorage.removeItem(`meeting_state_${meetingId}`);
                      const eventData = {
                        id: event.id,
                        title: event.title,
                        start: event.start,
                        end: event.end,
                        extendedProps: { ...event.extendedProps, isRunning: false, participant_count: 0 }
                      };
                      event.remove();
                      this.calendar.addEvent(eventData);
                    }
                  });
                  this.activeMeetings.clear();
                  this.hasActiveMeetings = false;
                  this.isPolling = false;
                  this.stopActiveMeetingsPolling();
                }
              },
              error: (xhr, status, error) => {
                this.isPolling = false;
              }
            });
          } else {
            this.closeAllPopovers(); // Fermer les popovers si aucune donnée d'événement
            this.activeMeetings.forEach(item => {
              const { meetingId, eventId, occurrenceDate } = JSON.parse(item);
              const uniqueEventId = occurrenceDate ? `${eventId}_${occurrenceDate}` : eventId;
              const event = this.calendar.getEventById(uniqueEventId);
              if (event) {
                event.setExtendedProp('isRunning', false);
                event.setExtendedProp('participant_count', 0);
                sessionStorage.removeItem(`meeting_state_${meetingId}`);
                const eventData = {
                  id: event.id,
                  title: event.title,
                  start: event.start,
                  end: event.end,
                  extendedProps: { ...event.extendedProps, isRunning: false, participant_count: 0 }
                };
                event.remove();
                this.calendar.addEvent(eventData);
              }
            });
            this.activeMeetings.clear();
            this.hasActiveMeetings = false;
            this.isPolling = false;
            this.stopActiveMeetingsPolling();
          }
        } catch (e) {
          this.isPolling = false;
        }
      },
      error: () => {
        this.isPolling = false;
      }
    });
  };

  poll();
  if (!this.activeMeetingsPollingInterval && document.visibilityState === 'visible') {
    this.activeMeetingsPollingInterval = setInterval(poll, 15000);
  }
},



 checkAndRestoreActiveMeetings() {
    const today = new Date();
    const startDate = this.formatDate(new Date(today.setFullYear(today.getFullYear() - 1)));
    const endDate = this.formatDate(new Date(today.setFullYear(today.getFullYear() + 2)));

    if (!this.selectedSchool) {
        this.hasActiveMeetings = false;
        this.stopActiveMeetingsPolling();
        return;
    }

    $.ajax({
        url: '<?php echo site_url('student/get_events'); ?>',
        type: 'GET',
        data: { 
            start_date: startDate, 
            end_date: endDate, 
            visio: 1, 
            school_id: this.selectedSchool, // Ajout de school_id
            [csrfName]: csrfHash 
        },
        success: (response) => {
            try {
                const data = JSON.parse(response);
                if (data.status === 'success' && data.data) {
                    let hasActive = false;
                    const meetingIdsToCheck = [];
                    data.data.forEach(event => {
                        const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
                        const isExpired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);
                        if (!isExpired && event.visio == 1 && event.occurrences) {
                            Object.keys(event.occurrences).forEach(occurrenceDate => {
                                const meetingId = event.occurrences[occurrenceDate]?.meeting_id;
                                if (meetingId) {
                                    const occurrenceEndDateTime = new Date(`${occurrenceDate}T${event.ending_time}`);
                                    const isOccurrenceExpired = occurrenceEndDateTime && (new Date() - occurrenceEndDateTime > 24 * 60 * 60 * 1000);
                                    if (!isOccurrenceExpired) {
                                        meetingIdsToCheck.push({ meetingId, eventId: event.id, occurrenceDate });
                                    }
                                }
                            });
                        }
                    });


                    if (meetingIdsToCheck.length > 0) {
                        $.ajax({
                            url: '<?php echo site_url('bigbluebutton/meeting_states'); ?>',
                            type: 'POST',
                            data: {
                                meetingIDs: meetingIdsToCheck.map(m => m.meetingId),
                                [csrfName]: csrfHash
                            },
                            dataType: 'json',
                            success: (stateResponse) => {
                                if (stateResponse.status === 'success' && stateResponse.data) {
                                    stateResponse.data.forEach(state => {
                                        const matching = meetingIdsToCheck.find(m => m.meetingId === state.meeting_id);
                                        if (matching) {
                                            const { eventId, occurrenceDate } = matching;
                                            const uniqueEventId = occurrenceDate ? `${eventId}_${occurrenceDate}` : eventId;
                                            const event = this.calendar.getEventById(uniqueEventId);
                                            if (event) {
                                                event.setExtendedProp('isRunning', state.is_running);
                                                event.setExtendedProp('participant_count', state.participant_count);
                                                event.setExtendedProp('meeting_id', state.meeting_id);
                                                // Force re-render to apply active-meeting class
                                                const eventData = {
                                                    id: event.id,
                                                    title: event.title,
                                                    start: event.start,
                                                    end: event.end,
                                                    extendedProps: {
                                                        ...event.extendedProps,
                                                        isRunning: state.is_running,
                                                        participant_count: state.participant_count,
                                                        meeting_id: state.meeting_id
                                                    }
                                                };
                                                event.remove();
                                                this.calendar.addEvent(eventData);
                                                if (state.is_running) {
                                                    hasActive = true;
                                                    this.activeMeetings.add(JSON.stringify({ meetingId: state.meeting_id, eventId, occurrenceDate }));
                                                    // Update modal UI if open
                                                    const modalEventId = String($('#eventId').val());
                                                    const modalOccurrenceDate = $('#currentOccurrenceDate').val();
                                                    if (modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                                        this.updateParticipantUI(eventId, state.participant_count, state.is_running, occurrenceDate);
                                                    }
                                                } else {
                                                    this.activeMeetings.delete(JSON.stringify({ meetingId: state.meeting_id, eventId, occurrenceDate }));
                                                    sessionStorage.removeItem(`meeting_state_${state.meeting_id}`);
                                                }
                                            }
                                        }
                                    });
                                    this.calendar.render();
                                    this.hasActiveMeetings = hasActive;
                                    if (hasActive && document.visibilityState === 'visible') {
                                        this.pollActiveMeetings();
                                    } else {
                                        this.stopActiveMeetingsPolling();
                                    }
                                }
                                csrfHash = stateResponse.csrf?.csrfHash || csrfHash;
                            },
                            error: (xhr) => {
                                console.error('checkAndRestoreActiveMeetings - Failed to fetch meeting states:', xhr.status, xhr.statusText);
                            }
                        });
                    } else {
                        this.hasActiveMeetings = false;
                        this.stopActiveMeetingsPolling();
                    }
                    csrfHash = data.csrf.csrfHash;
                } else {
                    this.hasActiveMeetings = false;
                    this.stopActiveMeetingsPolling();
                }
            } catch (e) {
                console.error('checkAndRestoreActiveMeetings - Error parsing response:', e, response);
            }
        },
        error: (xhr) => {
            console.error('checkAndRestoreActiveMeetings - AJAX error:', xhr.status, xhr.statusText);
            this.hasActiveMeetings = false;
            this.stopActiveMeetingsPolling();
        }
    });
},

stopActiveMeetingsPolling() {
  if (this.activeMeetingsPollingInterval) {
    this.lastHasActiveMeetings = this.hasActiveMeetings;
    clearInterval(this.activeMeetingsPollingInterval);
    this.activeMeetingsPollingInterval = null;
    this.hasActiveMeetings = false;
  }
},

  escapeHtml(str) {
    if (typeof str !== 'string') return '';
    return str.replace(/[&<>"']/g, (m) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[m]));
  },

  previousPeriod() { this.calendar.prev(); },
  nextPeriod() { this.calendar.next(); },
  goToToday() { this.calendar.today(); },

  bindGlobalEvents() {
  $('#schoolFilter').on('change', () => {
    this.selectedSchool = $('#schoolFilter').val();
    this.selectedClass = '';
    $('#classFilter').empty().append('<option value=""><?php echo get_phrase("All classes"); ?></option>').val('');
    this.clearEventCache(); // Clear event cache when school changes
    if (this.selectedSchool) {
      const view = this.calendar.view;
      const start = view.activeStart;
      const end = view.activeEnd;
      this.loadClassesWithEvents(start, end);
    }
    this.calendar.refetchEvents();
  });

  $('#classFilter').on('change', () => {
    this.selectedClass = $('#classFilter').val();
    this.clearEventCache(); // Clear event cache when class changes
    this.calendar.refetchEvents();
  });

  $('#viewFilter').on('change', () => {
    this.currentView = $('#viewFilter').val();
    this.calendar.changeView(this.currentView);
  });

  $('#joinMeetingBtn').on('click', () => { this.startMeeting(); });
}
};

$(document).ready(() => {
  const loadScripts = async () => {
    const scripts = [
      'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js',
      'https://cdn.jsdelivr.net/npm/sweetalert2@11'
      // 'https://cdn.jsdelivr.net/npm/socket.io-client@4.7.5/dist/socket.io.min.js'
    ];
    for (const src of scripts) {
      await new Promise((resolve) => {
        const script = document.createElement('script');
        script.src = src;
        script.onload = resolve;
        document.head.appendChild(script);
      });
    }
    CalendarApp.init();
  };
  loadScripts();
});
</script>