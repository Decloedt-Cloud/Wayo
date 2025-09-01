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
    <div class="modal fade mt-5" id="eventEditModal" tabindex="-1" role="dialog" aria-labelledby="eventEditModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventEditModalLabel"><?php echo get_phrase('event_details'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
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
                        <div>
                            Nombre de participants : <span id="participantCount">0</span>
                        </div>
                        <div class="form-group mt-3 btn-group-1">
                            <button type="button" class="btn join-meeting-btn" id="joinMeetingBtn" style="display: none;"><?php echo get_phrase('Start Meeting') ?></button>
                        </div>
                    </div>
                    <form id="eventForm" style="display: none;">
                        <input type="hidden" id="eventId" name="id">
                        <input type="hidden" id="recurrenceType" name="recurrence_type" value="does_not_repeat">
                        <input type="hidden" id="recurrenceEndDate" name="recurrence_end_date">
                        <input type="hidden" id="customRecurrence" name="custom_recurrence">
                    </form>
                </div>
            </div>
        </div>
    </div>
    

<script>
const csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
let csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

const CalendarApp = {
    calendar: null,
    ws: null,
    currentView: 'dayGridMonth',
    selectedClass: '',
    eventCache: {},
    isLoading: false,
    selectedDays: [],
     clearEventCache(eventId = null) {
        if (eventId) {
            delete this.eventCache[eventId];
        } else {
            this.eventCache = {};
        }
    },
    init() {
        this.initCalendar();
        this.bindGlobalEvents();
        this.loadClasses();
    },

    initCalendar() {
    const calendarEl = document.getElementById('calendar');

   this.ws = new WebSocket('ws://localhost:8080/ws');

    this.ws.onopen = () => {
        $('#connectionStatus').show();
        $('#connectionStatusText').text('Connected to real-time updates');
        this.startHeartbeat();
    };

    this.ws.onmessage = (event) => {
    try {
        const data = JSON.parse(event.data);
        if (!data.action) {
            console.warn('Received message without action:', event.data);
            return;
        }
        if (data.action === 'update_participants' && data.meetingID) {
            for (const eventId in this.eventCache) {
                const event = this.eventCache[eventId];
                const occurrenceDate = event.occurrence_date || event.starting_date;
                if (event.occurrences?.[occurrenceDate]?.meeting_id === data.meetingID) {
                    const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
                    const isExpired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);
                    if (isExpired) {
                        const unsubscribeMessage = JSON.stringify({
                            action: 'unsubscribe',
                            meetingID: data.meetingID
                        });
                        this.ws.send(unsubscribeMessage);
                        return;
                    }
                    this.eventCache[eventId].occurrences[occurrenceDate] = {
                        ...this.eventCache[eventId].occurrences[occurrenceDate],
                        participant_count: data.participantCount || 0,
                        is_running: data.isRunning || false
                    };
                    this.updateParticipantUI(eventId, data.participantCount, data.isRunning);
                    if (!data.isRunning) {
                        if (this.eventCache[eventId].occurrences[occurrenceDate].pollInterval) {
                            clearInterval(this.eventCache[eventId].occurrences[occurrenceDate].pollInterval);
                            delete this.eventCache[eventId].occurrences[occurrenceDate].pollInterval;
                        }
                        const unsubscribeMessage = JSON.stringify({
                            action: 'unsubscribe',
                            meetingID: data.meetingID
                        });
                        this.ws.send(unsubscribeMessage);
                    }
                    break;
                }
            }
        } else if (data.action === 'pong') {
        } else {
            console.warn('Unknown WebSocket message:', data);
        }
    } catch (e) {
        console.error('WebSocket message parsing error:', e, event.data);
        this.showNotification('error', 'Failed to process WebSocket message');
    }
};

    this.ws.onclose = () => {
        $('#connectionStatus').show();
        $('#connectionStatusText').text('Disconnected, attempting to reconnect...');
        setTimeout(() => {
            this.ws = new WebSocket('ws://localhost:8080/ws');
            this.ws.onopen = this.ws.onopen.bind(this);
            this.ws.onmessage = this.ws.onmessage.bind(this);
            this.ws.onclose = this.ws.onclose.bind(this);
            this.ws.onerror = this.ws.onerror.bind(this);
        }, 5000);
    };

    this.ws.onerror = (error) => {
        console.error('WebSocket error:', error);
        $('#connectionStatus').show();
        $('#connectionStatusText').text('WebSocket error, please try again later');
        this.showNotification('error', 'WebSocket connection failed');
    };

    this.calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: this.currentView,
        headerToolbar: false, // Use custom header
        views: {
            timeGridDay: {
                type: 'timeGrid',
                duration: { days: 1 }
            },
            listMonth: {
                type: 'list',
                duration: { months: 1 }
            }
        },
        events: (info, successCallback, failureCallback) => {
            this.loadEvents(info.startStr, info.endStr, successCallback, failureCallback);
        },
        eventClick: (info) => {
            info.jsEvent.preventDefault();
            this.showEventDetails(info.event.id, info.event.startStr.split('T')[0]);
        },
        eventMouseEnter: (info) => {
            if (!info.el || $('#createEventModal, #eventEditModal, #recurrenceModal').hasClass('show')) {
                return;
            }

            const el = info.el;
            const event = this.eventCache[info.event.id];
            if (!event) {
                return;
            }

            // Clear any existing popover
            if ($(el).data('bs.popover')) {
                try {
                    $(el).popover('dispose');
                } catch (e) {
                    console.warn('Error disposing existing popover:', e);
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
                    delay: { show: 300, hide: 150 },
                }).popover('show');
            } catch (e) {
                console.warn('Error initializing popover:', e);
            }
        },
        datesSet: (info) => {
            let displayText;
            switch (this.currentView) {
                case 'dayGridMonth':
                    displayText = info.view.calendar.getDate().toLocaleString('default', { month: 'long', year: 'numeric' });
                    break;
                case 'timeGridWeek':
                    displayText = `Week of ${this.formatDate(info.start)}`;
                    break;
                case 'timeGridDay':
                    displayText = this.formatDate(info.start);
                    break;
                case 'listMonth':
                    displayText = info.view.calendar.getDate().toLocaleString('default', { month: 'long', year: 'numeric' });
                    break;
                default:
                    displayText = info.view.calendar.getDate().toLocaleString('default', { month: 'long', year: 'numeric' });
            }
            $('#monthYear').text(displayText);
            this.loadClassesWithEvents(info.start, info.end);
        },
    eventContent: (arg) => {
    const event = this.eventCache[arg.event.id];
    // Calculate if the event is expired (end time is more than 24 hours ago)
    const isExpired = arg.event.end && (new Date() - new Date(arg.event.end) > 24 * 60 * 60 * 1000);
    const isVisio = event?.visio == 1;

    return {
        html: `
            <div class="fc-event-main ${isExpired ? 'expired' : ''}">
                ${isVisio ? '<i class="mdi mdi-video"></i>' : ''}
                <span class="event-title">${this.escapeHtml(arg.event.title)}</span>
                ${this.currentView === 'timeGridWeek' || this.currentView === 'timeGridDay' ? `<span class="event-time">(${arg.event.start.toTimeString().slice(0, 5)} - ${arg.event.end.toTimeString().slice(0, 5)})</span>` : ''}
            </div>
        `
    };
}
    });
    this.calendar.render();
},

    startHeartbeat() {
        setInterval(() => {
            if (this.ws.readyState === WebSocket.OPEN) {
                this.ws.send(JSON.stringify({ action: 'ping' }));
            }
        }, 30000);
    },

    formatDate(date) {
        if (!(date instanceof Date) || isNaN(date)) {
            return '';
        }
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    },

    loadEvents(start, end, successCallback, failureCallback) {
    if (this.isLoading) return;
    this.isLoading = true;

    $.ajax({
        url: '<?php echo site_url('student/get_events'); ?>',
        type: 'GET',
        data: {
            start_date: start.split('T')[0],
            end_date: end.split('T')[0],
            class_id: this.selectedClass,
            [csrfName]: csrfHash
        },
        beforeSend: () => {
            $('#calendar').addClass('loading');
        },
        success: (response) => {
            try {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    data.data.forEach(event => {
                        const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
                        event.is_expired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);
                        this.eventCache[event.id] = event;
                    });
                    const events = data.data.map(event => {
                        const endDate = event.ending_date || event.starting_date;
                        return {
                            id: event.id,
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
                                is_expired: event.is_expired
                            }
                        };
                    });
                    successCallback(events);
                    csrfHash = data.csrf.csrfHash;
                } 
            } catch (e) {
                console.error('Error parsing response:', e, response);
                this.showNotification('error', 'Invalid server response');
                failureCallback();
            }
        },
        complete: () => {
            this.isLoading = false;
            $('#calendar').removeClass('loading');
        }
    });
},

    loadClassesWithEvents(start, end) {
        $.ajax({
            url: '<?php echo site_url('student/get_user_school'); ?>',
            type: 'GET',
            data: { [csrfName]: csrfHash },
            success: (response) => {
                try {
                    const data = JSON.parse(response);
                    if (data.status === 'success') {
                        $.ajax({
                            url: '<?php echo site_url('student/get_classes_with_events'); ?>',
                            type: 'POST',
                            data: {
                                school_id: data.data.id,
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
                    } else {
                        this.showNotification('error', data.message);
                    }
                } catch (e) {
                    this.showNotification('error', 'Invalid server response');
                }
            },
            error: () => {
                this.showNotification('error', 'Failed to load school');
            }
        });
    },

    loadClasses() {
        $.ajax({
            url: '<?php echo site_url('student/get_user_school'); ?>',
            type: 'GET',
            data: { [csrfName]: csrfHash },
            success: (response) => {
                try {
                    const data = JSON.parse(response);
                    if (data.status === 'success') {
                        const editSchoolSelect = $('#school_id');
                        editSchoolSelect.empty();
                        editSchoolSelect.append(`<option value="${data.data.id}">${this.escapeHtml(data.data.name)}</option>`);
                        editSchoolSelect.prop('disabled', true);
                        csrfHash = data.csrf.csrfHash;
                    } else {
                        this.showNotification('error', data.message);
                    }
                } catch (e) {
                    this.showNotification('error', 'Invalid server response');
                }
            },
            error: () => {
                this.showNotification('error', 'Failed to load school');
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
            </div>
        `;
    },

    showEventDetails(eventId, occurrenceDate) {
        if (!eventId) {
        console.error('showEventDetails: No eventId provided');
        this.showNotification('error', 'No event selected');
        return;
    }

    const event = this.eventCache[eventId];
    if (!event) {
        console.warn(`Event not found in cache for eventId: ${eventId}, fetching from server`);
        $.ajax({
            url: '<?php echo site_url('student/get_event'); ?>',
            type: 'GET',
            data: {
                id: eventId,
                [csrfName]: csrfHash
            },
            success: (response) => {
                try {
                    const data = JSON.parse(response);
                    if (data.status === 'success' && data.data) {
                        const event = data.data;
                        const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
                        event.is_expired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);
                        this.eventCache[eventId] = event;
                        csrfHash = data.csrf.csrfHash;
                        this.showEventDetails(eventId, occurrenceDate); // Retry with fetched event
                    }
                } catch (e) {
                    console.error(`Error parsing get_event response for eventId ${eventId}:`, e, response);
                    this.showNotification('error', 'Invalid server response');
                }
            },
        });
        return;
    }

    event.occurrence_date = occurrenceDate;

        $('#eventTitle').text(this.escapeHtml(event.title || ''));
        $('#eventDescriptionView').text(event.description || 'No description');
        $('#eventSchool').text(event.school_name || event.title || '');
        $('#eventClass').text(event.class_name || '');
        $('#eventStart').text(`${event.starting_time ? event.starting_time.slice(0, 5) : ''}`);
        $('#eventEnd').text(`${event.ending_time ? event.ending_time.slice(0, 5) : ''}`);
        $('#eventRecurrenceSection').toggle(event.recurrence_type !== 'does_not_repeat');

        if (event.recurrence_type !== 'does_not_repeat') {
            let recurrenceText = event.recurrence_type.charAt(0).toUpperCase() + event.recurrence_type.slice(1);
            if (event.recurrence_type === 'weekly' && event.custom_recurrence) {
                try {
                    const days = JSON.parse(event.custom_recurrence);
                    if (Array.isArray(days) && days.length > 0) {
                        recurrenceText += ` on ${days.join(', ')}`;
                    }
                } catch (e) {
                    console.error('showEventDetails: Erreur parsing custom_recurrence:', e);
                }
            }
            let endDateText = event.recurrence_end_date
                ? ` until ${event.recurrence_end_date}`
                : ` until ${this.formatDate(new Date(new Date(event.starting_date).setFullYear(new Date(event.starting_date).getFullYear() + 1)))}`;
            $('#eventRecurrence').text(recurrenceText + endDateText);
        } else {
            $('#eventRecurrence').text('');
        }

        const isVisio = event.visio == 1;
        const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
        const isExpired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);
        $('#eventParticipantsSection').toggle(isVisio);
        $('#joinMeetingBtn').toggle(isVisio).prop('disabled', isExpired);
        $('#editEventBtn').prop('disabled', isExpired);
        $('#deleteevent').prop('disabled', false);

        if (isExpired) {
            $('#joinMeetingBtn, #editEventBtn').addClass('disabled');
            $('#deleteevent').removeClass('disabled');
        } else {
            $('#joinMeetingBtn, #editEventBtn, #deleteevent').removeClass('disabled');
        }

        const occurrenceData = event.occurrences?.[occurrenceDate] || {};
        $('#joinMeetingBtn').text(isVisio && occurrenceData.is_running ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Start Meeting'); ?>');

        // Peuplement des champs du formulaire d'édition
        $('#eventId').val(event.id || '');
        $('#eventTitleInput').val(this.escapeHtml(event.title || ''));
        $('#eventDescription').val(event.description || '');
        $('#school_id').val(event.school_id || '');
        $('#classe_id').val(event.class_id || '');
        $('#eventDate').val(event.starting_date || '');
        $('#eventStartTime').val(event.starting_time ? event.starting_time.slice(0, 5) : '');
        $('#eventEndTime').val(event.ending_time ? event.ending_time.slice(0, 5) : '');
        $('#recurrenceType').val(event.recurrence_type || 'does_not_repeat');
        $('#recurrenceEndDate').val(event.recurrence_end_date || '');
        $('#customRecurrence').val(event.custom_recurrence || '');
        $('#visio').prop('checked', event.visio == 1);

        // Initialiser le modal de récurrence pour l'édition
        $('#recurrenceModal').on('show.bs.modal', () => {
            const targetForm = $('#createEventModal').hasClass('show') ? $('#createEventForm') : $('#eventForm');
            const eventDate = targetForm.find('[name="start"]').val();
            const recurrenceType = targetForm.find('[name="recurrence_type"]').val();

            $('#recurrenceTypePopup').val(recurrenceType || 'does_not_repeat');
            $('#recurrenceEndDatePopup').val(targetForm.find('[name="recurrence_end_date"]').val() || '');
            $('#customRecurrencePopup').val(targetForm.find('[name="custom_recurrence"]').val() || '');
            $('#recurrenceStartDatePopup').val(eventDate || '');
            $('#recurrenceStartDateSection').toggle(recurrenceType === 'monthly' || recurrenceType === 'yearly');

            this.selectedDays = [];
            if (recurrenceType === 'weekly' && targetForm.find('[name="custom_recurrence"]').val()) {
                try {
                    const days = JSON.parse(targetForm.find('[name="custom_recurrence"]').val());
                    if (Array.isArray(days)) {
                        this.selectedDays = days;
                        $('.day-btn').each((_, btn) => {
                            const day = $(btn).data('day');
                            $(btn).toggleClass('active', days.includes(day));
                        });
                        $('#daySelection').show();
                    }
                } catch (e) {
                    console.error('showEventDetails: Erreur parsing custom_recurrence:', e);
                }
            } else if (recurrenceType === 'daily') {
                this.selectedDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                $('.day-btn').addClass('active');
                $('#daySelection').show();
            } else {
                $('.day-btn').removeClass('active');
                $('#daySelection').hide();
            }

            $('#recurrenceStartDatePopup').off('change').on('change', () => {
                targetForm.find('[name="start"]').val($('#recurrenceStartDatePopup').val());
            });

            targetForm.find('[name="start"]').off('change').on('change', () => {
                $('#recurrenceStartDatePopup').val(targetForm.find('[name="start"]').val());
            });
        });

        if (event.school_id) {
            $.ajax({
                url: '<?php echo site_url('student/get_classes_by_school'); ?>',
                type: 'POST',
                data: { school_id: event.school_id, [csrfName]: csrfHash },
                success: (response) => {
                    try {
                        const data = JSON.parse(response);
                        if (data.status === 'success') {
                            const classSelect = $('#classe_id');
                            classSelect.empty();
                            classSelect.append('<option value=""><?php echo get_phrase("select_a_class"); ?></option>');
                            data.classes.forEach(cls => {
                                classSelect.append(`<option value="${cls.id}" ${cls.id == event.class_id ? 'selected' : ''}>${this.escapeHtml(cls.name)}</option>`);
                            });
                            csrfHash = data.csrf.csrfHash;
                        } else {
                            this.showNotification('error', data.message);
                        }
                    } catch (e) {
                        this.showNotification('error', 'Invalid server response');
                    }
                },
                error: () => {
                    this.showNotification('error', 'Failed to load classes');
                }
            });
        }

       
        $('#eventDetailsView').show();
        $('#eventForm').hide();
        $('#eventEditModal').modal('show');
    },

    

    escapeHtml(str) {
        return str.replace(/[&<>"']/g, (m) => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        }[m]));
    },



    bindGlobalEvents() {
        $('#classFilter').on('change', () => {
            this.selectedClass = $('#classFilter').val();
            this.calendar.refetchEvents();
        });

        $('#viewFilter').on('change', () => {
            this.currentView = $('#viewFilter').val();
            this.calendar.changeView(this.currentView);
        });
        $('#joinMeetingBtn').on('click', () => {
            this.startMeeting();
        });
    },

    previousPeriod() {
        this.calendar.prev();
    },

    nextPeriod() {
        this.calendar.next();
    },

    goToToday() {
        this.calendar.today();
    },

    pollParticipantCount(eventId, meetingId, occurrenceDate) {
    if (!eventId || !meetingId) {
        console.error('pollParticipantCount: Missing eventId or meetingId');
        return;
    }

    const event = this.eventCache[eventId];
    if (!event) {
        console.warn(`pollParticipantCount: Event ${eventId} not found in cache`);
        return;
    }

    const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
    const isExpired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);

    if (isExpired) {
        return;
    }

    if (this.ws.readyState === WebSocket.OPEN) {
        return;
    }

    if (!this.eventCache[eventId].occurrences) {
        this.eventCache[eventId].occurrences = {};
    }
    if (!this.eventCache[eventId].occurrences[occurrenceDate]) {
        this.eventCache[eventId].occurrences[occurrenceDate] = {};
    }

    const pollInterval = setInterval(() => {
        const currentEndDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
        const currentlyExpired = currentEndDateTime && (new Date() - currentEndDateTime > 24 * 60 * 60 * 1000);

        if (currentlyExpired) {
            clearInterval(pollInterval);
            delete this.eventCache[eventId].occurrences[occurrenceDate].pollInterval;
            return;
        }

        $.ajax({
            url: '<?php echo site_url('bigbluebutton/update_participant_count'); ?>',
            type: 'POST',
            data: {
                meeting_id: meetingId,
                [csrfName]: csrfHash
            },
            dataType: 'json',
            success: (data) => {
                if (data.status === 'success') {
                    if (this.eventCache[eventId]) {
                        this.eventCache[eventId].occurrences[occurrenceDate] = {
                            ...this.eventCache[eventId].occurrences[occurrenceDate],
                            participant_count: data.participant_count || 0,
                            is_running: data.is_running || false
                        };
                        this.updateParticipantUI(eventId, data.participant_count, data.is_running);
                        if (!data.is_running) {
                            clearInterval(pollInterval);
                            delete this.eventCache[eventId].occurrences[occurrenceDate].pollInterval;
                            if (this.ws.readyState === WebSocket.OPEN) {
                                const unsubscribeMessage = JSON.stringify({
                                    action: 'unsubscribe',
                                    meetingID: meetingId
                                });
                                this.ws.send(unsubscribeMessage);
                            }
                        }
                    }
                    csrfHash = data.csrf?.csrfHash || csrfHash;
                } else {
                    console.error(`Failed to poll participant count for meetingID: ${meetingId}:`, data.message);
                    if (data.message === 'Meeting has ended' || data.message === 'No active appointment found' || data.message === 'Meeting is not running') {
                        clearInterval(pollInterval);
                        delete this.eventCache[eventId].occurrences[occurrenceDate].pollInterval;
                        if (this.ws.readyState === WebSocket.OPEN) {
                            const unsubscribeMessage = JSON.stringify({
                                action: 'unsubscribe',
                                meetingID: meetingId
                            });
                            this.ws.send(unsubscribeMessage);
                        }
                    }
                }
            },
            error: (xhr) => {
                console.error(`AJAX error polling participant count for meetingID: ${meetingId}:`, xhr.status, xhr.statusText);
                this.showNotification('error', 'Failed to fetch participant count');
            }
        });
    }, 5000); // Poll every 5 seconds as fallback

    this.eventCache[eventId].occurrences[occurrenceDate].pollInterval = pollInterval;

    $('#eventEditModal').off('hidden.bs.modal.poll').on('hidden.bs.modal.poll', () => {
        clearInterval(pollInterval);
        delete this.eventCache[eventId].occurrences[occurrenceDate].pollInterval;
        if (this.ws.readyState === WebSocket.OPEN) {
            const unsubscribeMessage = JSON.stringify({
                action: 'unsubscribe',
                meetingID: meetingId
            });
            this.ws.send(unsubscribeMessage);
        }
    });
},

updateParticipantUI(eventId, participantCount, isRunning) {
    clearTimeout(this.debounceTimeout);
    this.debounceTimeout = setTimeout(() => {
        if ($('#eventId').val() === eventId) {
            $('#participantCount').text(participantCount || 0);
            $('#joinMeetingBtn').text(isRunning ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Start Meeting'); ?>');
        }
    }, 100);
},

    startMeeting() {
    const eventId = $('#eventId').val();
    if (!eventId) {
        console.error('startMeeting: No eventId found in #eventId input');
        this.showNotification('error', 'No event selected');
        return;
    }

    let event = this.eventCache[eventId];
    if (!event) {
        console.warn(`Event not found in cache for eventId: ${eventId}, attempting to fetch from server`);
        $.ajax({
            url: '<?php echo site_url('student/get_event'); ?>',
            type: 'GET',
            data: {
                id: eventId,
                [csrfName]: csrfHash
            },
            async: false,
            success: (response) => {
                try {
                    const data = JSON.parse(response);
                    if (data.status === 'success' && data.data) {
                        event = data.data;
                        const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
                        event.is_expired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);
                        this.eventCache[eventId] = event;
                        csrfHash = data.csrf.csrfHash;
                    } 
                } catch (e) {
                    console.error(`Error parsing get_event response for eventId ${eventId}:`, e, response);
                    this.showNotification('error', 'Invalid server response');
                }
            }
        });
    }

    if (!event) {
        console.error(`Event not found for eventId: ${eventId} after server fetch`);
        this.showNotification('error', 'Event not found');
        return;
    }

    if (event.visio != 1) {
        console.error(`Event ${eventId} does not support video conferencing`);
        this.showNotification('error', 'This event does not support video conferencing');
        return;
    }

    const occurrenceDate = event.occurrence_date || event.starting_date;
    const occurrenceData = event.occurrences?.[occurrenceDate] || {};
    const buttonText = $('#joinMeetingBtn').text();

    if (buttonText === '<?php echo get_phrase('Start Meeting'); ?>') {
        $.ajax({
            url: '<?php echo site_url('student/start_meeting'); ?>',
            type: 'POST',
            data: {
                event_id: eventId,
                occurrence_date: occurrenceDate,
                [csrfName]: csrfHash
            },
            success: (response) => {
                try {
                    const data = JSON.parse(response);
                    csrfHash = data.csrf.csrfHash;

                    if (data.status === 'success' && data.meeting_id && data.appointment_id) {
                        if (!this.eventCache[eventId].occurrences) {
                            this.eventCache[eventId].occurrences = {};
                        }
                        this.eventCache[eventId].occurrences[occurrenceDate] = {
                            meeting_id: data.meeting_id,
                            appointment_id: data.appointment_id,
                            participant_count: data.participant_count || 0,
                            is_running: data.is_running || false
                        };
                        
                        // Mettre à jour le compteur immédiatement
                        $('#participantCount').text(data.participant_count || 0);
                        $('#joinMeetingBtn').text(data.is_running ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Start Meeting'); ?>');

                        // S'abonner aux mises à jour WebSocket
                        if (this.ws.readyState === WebSocket.OPEN) {
                            const subscribeMessage = JSON.stringify({
                                action: 'subscribe',
                                meetingID: data.meeting_id
                            });
                            this.ws.send(subscribeMessage);
                        } else {
                            console.warn('WebSocket not connected, cannot subscribe to meetingID:', data.meeting_id);
                            this.showNotification('warning', 'Unable to connect to real-time updates. Using polling instead.');
                        }

                        // Démarrer le polling pour les mises à jour des participants
                        this.pollParticipantCount(eventId, data.meeting_id, occurrenceDate);

                        const joinUrl = data.join_url || '<?php echo site_url('bigbluebutton/join_meeting'); ?>/' + encodeURIComponent(data.meeting_id);

                        const newWindow = window.open(joinUrl, '_blank');
                        if (!newWindow || newWindow.closed || typeof newWindow.closed === 'undefined') {
                            this.showNotification('warning', 'Unable to open meeting. Please allow pop-ups for this site or click <a href="' + joinUrl + '" target="_blank">here</a> to join.', 5000);
                        } else {
                            this.showNotification('success', 'Starting meeting...');
                            $('#eventEditModal').modal('hide');
                        }
                    } else {
                        console.error(`start_meeting failed for event: ${eventId}, occurrence: ${occurrenceDate}:`, data.message);
                        this.showNotification('error', data.message || 'Failed to start meeting');
                    }
                } catch (e) {
                    console.error(`start_meeting: Error parsing response for event: ${eventId}, occurrence: ${occurrenceDate}:`, e, response);
                    this.showNotification('error', 'Invalid server response');
                }
            },
            error: (xhr) => {
                console.error(`start_meeting: AJAX error for event: ${eventId}, occurrence: ${occurrenceDate}:`, xhr.status, xhr.statusText);
                this.showNotification('error', 'Error starting meeting. Please check server connectivity.');
            }
        });
    } else if (buttonText === '<?php echo get_phrase('Join Meeting'); ?>') {
        if (!occurrenceData.meeting_id || !occurrenceData.appointment_id) {
            console.error(`No meeting information for event: ${eventId}, occurrence: ${occurrenceDate}`);
            this.showNotification('error', 'No meeting information available. Please start the meeting first.');
            return;
        }

        // Démarrer le polling pour les mises à jour des participants
        this.pollParticipantCount(eventId, occurrenceData.meeting_id, occurrenceDate);

        const joinUrl = '<?php echo site_url('bigbluebutton/join_meeting'); ?>/' + encodeURIComponent(occurrenceData.meeting_id);

        const newWindow = window.open(joinUrl, '_blank');
        if (!newWindow || newWindow.closed || typeof newWindow.closed === 'undefined') {
            this.showNotification('warning', 'Unable to open meeting. Please allow pop-ups for this site or click <a href="' + joinUrl + '" target="_blank">here</a> to join.', 5000);
        } else {
            this.showNotification('success', 'Joining meeting...');
            $('#eventEditModal').modal('hide');
        }
    }
},

    
};

$(document).ready(() => {
    // Load FullCalendar dependencies
    const loadScripts = async () => {
    const scripts = [
        'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js',
        'https://cdn.jsdelivr.net/npm/sweetalert2@11'
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