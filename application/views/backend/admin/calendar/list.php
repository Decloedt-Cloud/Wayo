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
                    <button class="add-event-btn" data-bs-toggle="modal" data-bs-target="#createEventModal"><i class="mdi mdi-plus"></i><?php echo get_phrase('New_Event'); ?></button>
                </div>
            </div>
        </div>
    </div>
    <!-- FullCalendar container -->
    <div id="calendar"></div>
    <!-- Modals remain unchanged -->
    <div class="modal fade mt-5" id="createEventModal" tabindex="-1" role="dialog" aria-labelledby="createEventModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="createEventModalLabel"><?php echo get_phrase('New_Event'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="createEventForm">
                        <input type="hidden" id="createRecurrenceType" name="recurrence_type" value="does_not_repeat">
                        <input type="hidden" id="createRecurrenceEndDate" name="recurrence_end_date">
                        <input type="hidden" id="createCustomRecurrence" name="custom_recurrence">
                        <div class="form-group">
                            <span class="mdi mdi-format-title"></span>
                            <label for="createeventTitle"><span class="required required-input"> * </span></label>
                            <input type="text" class="form-control" id="createeventTitle" name="title" placeholder="<?php echo get_phrase('Title'); ?>" required>
                        </div>
                        <div class="form-group mt-3">
                            <span class="mdi mdi-text"></span>
                            <label for="createeventDescription"></label>
                            <textarea class="form-control" id="createeventDescription" name="description" rows="3" placeholder="<?php echo get_phrase('Description'); ?>"></textarea>
                        </div>
                        <div class="form-group-community-class mt-3">
                            <span class="mdi mdi-account-multiple"></span>
                            <div class="input-container">
                                <div class="form-group">
                                    <label for="createSchoolId"><span class="required"> * </span></label>
                                    <select class="form-control" id="createSchoolId" name="school_id" required>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="createClasseId"><span class="required"> * </span></label>
                                    <select class="form-control" id="createClasseId" name="classe_id" required>
                                        <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-date-time mt-3">
                            <span class="mdi mdi-clock-time-three-outline"></span>
                            <div class="input-container">
                                <div class="form-group">
                                    <label for="createeventDate"><span class="required"> * </span></label>
                                    <input type="date" class="form-control" id="createeventDate" name="start" required min="">
                                </div>
                                <div class="form-group">
                                    <label for="createeventStartTime"><span class="required"> * </span></label>
                                    <select class="form-control" id="createeventStartTime" name="start_time" required>
                                        <option value=""><?php echo get_phrase('Start time'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-date-time mt-3">
                            <span class="mdi mdi-clock-time-three-outline"></span>
                            <div class="input-container">
                                    <div class="form-group">
                                        <input type="date" class="form-control" id="createeventEndDate" name="end_date" min="">
                                    </div>
                                        <div class="form-group">
                                            <label for="createeventEndTime"><span class="required"> * </span></label>
                                            <select class="form-control" id="createeventEndTime" name="end_time" required>
                                                <option value=""><?php echo get_phrase('End time'); ?></option>
                                                <?php
                                                for ($h = 0; $h < 24; $h++) {
                                                    for ($m = 0; $m < 60; $m += 15) {
                                                        $time = sprintf("%02d:%02d", $h, $m);
                                                        echo "<option value=\"$time\">$time</option>";
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <span class="mdi mdi-repeat"></span>
                            <button type="button" class="btn recurrence-btn" data-bs-toggle="modal" data-bs-target="#recurrenceModal"><?php echo get_phrase('Repeat') ?></button>
                        </div>
                        <div class="form-group mt-2">
                            <span class="mdi mdi-video"></span>
                            <label for="createVisio" style="margin-left: 15px;"><?php echo get_phrase('Visio'); ?></label>
                            <label class="toggle-switch">
                                <input type="checkbox" id="createVisio" name="visio">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="form-group mt-3 col-md-12">
                            <button type="submit" class="btn btn-primary" style="border-radius: 6px;"><?php echo get_phrase('Save') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
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
                        <div><?php echo get_phrase('Nombre de participants :'); ?>
                             <span id="participantCount">0</span>
                        </div>
                        <div class="form-group mt-3 btn-group-1">
                            <button type="button" class="btn btn-primary" id="editEventBtn" style="border-radius: 7px;"><?php echo get_phrase('Edit') ?></button>
                            <button type="button" class="btn btn-danger" id="deleteevent" style="border-radius: 7px;"><?php echo get_phrase('Delete') ?></button>
                            <button type="button" class="btn join-meeting-btn" id="joinMeetingBtn" style="display: none;"><?php echo get_phrase('Start Meeting') ?></button>
                        </div>
                    </div>
                    <form id="eventForm" style="display: none;">
                        <input type="hidden" id="eventId" name="id">
                        <input type="hidden" id="recurrenceType" name="recurrence_type" value="does_not_repeat">
                        <input type="hidden" id="recurrenceEndDate" name="recurrence_end_date">
                        <input type="hidden" id="customRecurrence" name="custom_recurrence">
                        <div class="form-group">
                            <span class="mdi mdi-format-title"></span>
                            <label for="eventTitleInput"><span class="required"> * </span></label>
                            <input type="text" class="form-control" id="eventTitleInput" name="title" placeholder="<?php echo get_phrase('Title') ?>" required>
                        </div>
                        <div class="form-group mt-3">
                            <span class="mdi mdi-text"></span>
                            <label for="eventDescription"></label>
                            <textarea class="form-control" id="eventDescription" name="description" rows="3" placeholder="<?php echo get_phrase("Description") ?>"></textarea>
                        </div>
                        <div class="form-group-community-class mt-3">
                            <span class="mdi mdi-account-multiple"></span>
                            <div class="input-container">
                                <div class="form-group">
                                    <label for="school_id"><span class="required"> * </span></label>
                                    <select class="form-control" id="school_id" name="school_id" required>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="classe_id"><span class="required"> * </span></label>
                                    <select class="form-control" id="classe_id" name="classe_id" required>
                                        <option value=""><?php echo get_phrase('select_a_class'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-date-time mt-3">
                            <span class="mdi mdi-clock-time-three-outline"></span>
                            <div class="input-container">
                                <div class="form-group">
                                    <label for="eventDate"><span class="required"> * </span></label>
                                    <input type="date" class="form-control" id="eventDate" name="start" required min="">
                                </div>
                                <div class="form-group">
                                    <label for="eventStartTime"><span class="required"> * </span></label>
                                    <select class="form-control" id="eventStartTime" name="start_time" required>
                                        <option value=""><?php echo get_phrase('start time'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group-date-time mt-3">
                            <span class="mdi mdi-clock-time-three-outline"></span>
                            <div class="input-container">
                                     <div class="form-group">
                                    <label for="eventEndDate"></label>
                                    <input type="date" class="form-control" id="eventEndDate" name="end_date" min="">
                                </div>
                                <div class="form-group">
                                    <label for="eventEndTime"><span class="required"> * </span></label>
                                    <select class="form-control" id="eventEndTime" name="end_time" required>
                                        <option value=""><?php echo get_phrase('End time'); ?></option>
                                        <?php
                                        for ($h = 0; $h < 24; $h++) {
                                            for ($m = 0; $m < 60; $m += 15) {
                                                $time = sprintf("%02d:%02d", $h, $m);
                                                echo "<option value=\"$time\">$time</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group mt-2">
                            <span class="mdi mdi-repeat"></span>
                            <button type="button" class="btn recurrence-btn" data-bs-toggle="modal" data-bs-target="#recurrenceModal"><?php echo get_phrase('Repeat') ?></button>
                        </div>
                        <div class="form-group mt-2">
                            <span class="mdi mdi-video"></span>
                            <label for="visio" style="margin-left: 15px;"><?php echo get_phrase('Visio'); ?></label>
                            <label class="toggle-switch">
                                <input type="checkbox" id="visio" name="visio">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="form-group mt-3 col-md-12 btn-group-1">
                            <button type="submit" class="btn btn-primary"><?php echo get_phrase('Save') ?></button>
                            <button type="button" class="btn btn-secondary" id="cancelEditBtn"><?php echo get_phrase('Cancel') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" style="top:20%; z-index: 1070;" id="recurrenceModal" tabindex="-1" role="dialog" aria-labelledby="recurrenceModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="recurrenceModalLabel"><?php echo get_phrase('repeat') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="recurrenceForm">
                        <div class="form-group mb-2">
                            <span class="mdi mdi-calendar-sync"></span>
                            <label for="recurrenceTypePopup"></label>
                            <select class="form-control" id="recurrenceTypePopup" name="recurrence_type">
                                <option value="does_not_repeat"><?php echo get_phrase('does_not_repeat'); ?></option>
                                <option value="daily"><?php echo get_phrase('day'); ?></option>
                                <option value="weekly"><?php echo get_phrase('week'); ?></option>
                                <option value="monthly"><?php echo get_phrase('months'); ?></option>
                                <option value="yearly"><?php echo get_phrase('year'); ?></option>
                            </select>
                        </div>
                        <div class="form-group mb-2" id="recurrenceStartDateSection" style="display: none;">
                        <span class="mdi mdi-calendar-start"></span>
                        <label for="recurrenceStartDatePopup"></label>
                        <input type="date" class="form-control" id="recurrenceStartDatePopup" name="recurrence_start_date">
                        </div>
                        <div class="form-group mb-2" id="daySelection" style="display: none;">
                            <span class="mdi mdi-calendar-week"></span>
                            <label></label>
                            <div class="d-flex justify-content-between gap-3">
                                <button type="button" class="btn btn-outline-primary day-btn" data-day="Monday">M</button>
                                <button type="button" class="btn btn-outline-primary day-btn" data-day="Tuesday">T</button>
                                <button type="button" class="btn btn-outline-primary day-btn" data-day="Wednesday">W</button>
                                <button type="button" class="btn btn-outline-primary day-btn" data-day="Thursday">T</button>
                                <button type="button" class="btn btn-outline-primary day-btn" data-day="Friday">F</button>
                                <button type="button" class="btn btn-outline-primary day-btn" data-day="Saturday">S</button>
                                <button type="button" class="btn btn-outline-primary day-btn" data-day="Sunday">S</button>
                            </div>
                        </div>
                        <div class="form-group">
                        <div style="display: flex; align-items: center;">
                            <span class="mdi mdi-calendar-end"></span>
                            <input type="date" class="form-control" id="recurrenceEndDatePopup" name="recurrence_end_date">
                        </div>
                        </div>
                        <label for="recurrenceEndDatePopup" style="display: block; margin-top: 5px; margin-left: 32px; font-size: 0.8rem; color: #6c757d;">
                            <small><?php echo get_phrase('leave_blank_for_default_one_year'); ?></small>
                        </label>
                        <div class="form-group mb-2 hidden">
                            <label for="customRecurrencePopup"><?php echo get_phrase('Day_selected'); ?></label>
                            <input type="text" class="form-control" id="customRecurrencePopup" name="custom_recurrence" placeholder="<?php echo get_phrase('exemple_cron'); ?>" readonly>
                        </div>
                        <div class="form-group mt-2 btn-group-1">
                            <button type="button" class="btn btn-primary" id="saveRecurrence"><?php echo get_phrase('save') ?></button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?php echo get_phrase('Annuler') ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="DynamicNotification" class="toast align-items-center text-white border-0 position-fixed top-0 end-0 p-2 m-3" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/socket.io-client@4.7.5/dist/socket.io.min.js"></script>
<script>
const csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
let csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

const CalendarApp = {
    calendar: null,
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
    const now = new Date();
    for (const eventId in this.eventCache) {
        const event = this.eventCache[eventId];
        const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
        if (endDateTime && (now - endDateTime > 24 * 60 * 60 * 1000)) {
            delete this.eventCache[eventId];
        }
    }

    this.socket = io('https://preprod.wayo.site', { transports: ['websocket'] });

    this.socket.on('connect', () => {
        $('#connectionStatus').show();
        $('#connectionStatusText').text('Connected to real-time updates');
        this.startHeartbeat();
        this.socket.on('connect_error', (error) => {
    console.error('Socket.IO connection error:', error);
});
this.socket.on('disconnect', (reason) => {
    console.error('Socket.IO disconnected:', reason);
});
        Object.values(this.eventCache).forEach(event => {
            const occurrenceDate = event.occurrence_date || event.starting_date;
            const occurrenceData = event.occurrences?.[occurrenceDate] || {};
            if (event.visio == 1 && occurrenceData.meeting_id && !occurrenceData.is_expired) {
                this.socket.emit('subscribe', { meetingID: occurrenceData.meeting_id });
                this.socket.emit('request_current_state', { meetingID: occurrenceData.meeting_id });
            }
        });
    });

   this.socket.on('update_participants', (data) => {
    try {
        if (!data.meetingID) {
            console.warn('Received update_participants without meetingID:', data);
            return;
        }
        for (const eventId in this.eventCache) {
            const event = this.eventCache[eventId];
            const occurrenceDate = event.occurrence_date || event.starting_date;
            if (event.occurrences?.[occurrenceDate]?.meeting_id === data.meetingID) {
                const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
                const isExpired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);
                if (isExpired) {
                    this.socket.emit('unsubscribe', { meetingID: data.meetingID });
                    return;
                }
                this.eventCache[eventId].occurrences[occurrenceDate] = {
                    ...this.eventCache[eventId].occurrences[occurrenceDate],
                    participant_count: data.participantCount || 0,
                    is_running: data.isRunning || false
                };
                this.updateParticipantUI(eventId, data.participantCount, data.isRunning);
                break;
            }
        }
    } catch (e) {
        console.error('Socket.IO update_participants processing error:', e, data);
        this.showNotification('error', 'Failed to process real-time update');
    }
});

this.socket.on('current_state', (data) => {
    try {
        if (!data.meetingID) {
            console.warn('Received current_state without meetingID:', data);
            return;
        }
        for (const eventId in this.eventCache) {
            const event = this.eventCache[eventId];
            const occurrenceDate = event.occurrence_date || event.starting_date;
            if (event.occurrences?.[occurrenceDate]?.meeting_id === data.meetingID) {
                const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
                const isExpired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);
                if (isExpired) {
                    this.socket.emit('unsubscribe', { meetingID: data.meetingID });
                    return;
                }
                this.eventCache[eventId].occurrences[occurrenceDate] = {
                    ...this.eventCache[eventId].occurrences[occurrenceDate],
                    participant_count: data.participantCount || 0,
                    is_running: data.isRunning || false
                };
                this.updateParticipantUI(eventId, data.participantCount, data.isRunning);
                break;
            }
        }
    } catch (e) {
        console.error('Socket.IO current_state processing error:', e, data);
        this.showNotification('error', 'Failed to process current state update');
    }
});

    this.socket.on('error', (data) => {
        console.error('Socket.IO error:', data.message);
        this.showNotification('error', data.message || 'Real-time update error');
    });

    this.socket.on('disconnect', () => {
        $('#connectionStatus').show();
        $('#connectionStatusText').text('Disconnected, attempting to reconnect...');
        this.showNotification('warning', 'Disconnected from real-time updates');
    });

    this.socket.on('pong', () => {
        // Heartbeat response
    });

    // Le reste de la méthode initCalendar reste inchangé
    this.calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: this.currentView,
        headerToolbar: false,
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
        this.socket.emit('ping');
    }, 30000);
},

    generateTimeOptions(selectElement, selectedDate) {
    const now = new Date();
    const today = this.formatDate(now);
    const isToday = selectedDate === today;
    const currentHour = now.getHours();
    const currentMinute = now.getMinutes();

    // Vider les options existantes sauf le placeholder
    selectElement.find('option:not(:first)').remove();

    // Générer les options d'heure
    for (let h = 0; h < 24; h++) {
        for (let m = 0; m < 60; m += 15) {
            // Sauter les heures/minutes passées si la date est aujourd'hui
            if (isToday && (h < currentHour || (h === currentHour && m <= currentMinute))) {
                continue;
            }
            const time = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
            selectElement.append(`<option value="${time}">${time}</option>`);
        }
    }
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
        url: '<?php echo site_url('admin/get_events'); ?>',
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
        $.ajax({
            url: '<?php echo site_url('admin/get_user_school'); ?>',
            type: 'GET',
            data: { [csrfName]: csrfHash },
            success: (response) => {
                try {
                    const data = JSON.parse(response);
                    if (data.status === 'success') {
                        $.ajax({
                            url: '<?php echo site_url('admin/get_classes_with_events'); ?>',
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
            url: '<?php echo site_url('admin/get_user_school'); ?>',
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

    subscribeToMeeting(eventId, occurrenceDate) {
    const meetingID = this.eventCache[eventId]?.occurrences?.[occurrenceDate]?.meeting_id;
    if (meetingID) {
        this.socket.emit('subscribe', { meetingID });
    } else {
        console.warn(`No meetingID found for eventId: ${eventId}, occurrenceDate: ${occurrenceDate}`);
    }
},
 showEventDetails(eventId, occurrenceDate) {
    $('#eventId').val(eventId);
    if (!eventId) {
        console.error('showEventDetails: No eventId provided');
        this.showNotification('error', 'No event selected');
        return;
    }

    const event = this.eventCache[eventId];
    if (!event) {
        console.warn(`Event not found in cache for eventId: ${eventId}, fetching from server`);
        $.ajax({
            url: '<?php echo site_url('admin/get_event'); ?>',
            type: 'GET',
            data: { id: eventId, [csrfName]: csrfHash },
            success: (response) => {
                try {
                    const data = JSON.parse(response);
                    if (data.status === 'success' && data.data) {
                        const event = data.data;
                        const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
                        event.is_expired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);
                        event.occurrences = event.occurrences || {};
                        event.occurrences[occurrenceDate] = event.occurrences[occurrenceDate] || {};
                        this.eventCache[eventId] = event;
                        csrfHash = data.csrf.csrfHash;
                        this.showEventDetails(eventId, occurrenceDate); // Retry with updated data
                    } else {
                        this.showNotification('error', data.message || 'Failed to load event');
                    }
                } catch (e) {
                    console.error(`Error parsing get_event response for eventId ${eventId}:`, e, response);
                    this.showNotification('error', 'Invalid server response');
                }
            },
            error: () => {
                this.showNotification('error', 'Failed to load event');
            }
        });
        return;
    }

    // Ensure occurrences object is initialized
    if (!event.occurrences) {
        event.occurrences = {};
    }
    event.occurrence_date = occurrenceDate;
    const occurrenceData = event.occurrences[occurrenceDate] || {};
    const isVisio = event.visio == 1;
    const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
    const isExpired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);

    // Set basic event details
    $('#eventTitle').text(this.escapeHtml(event.title || ''));
    $('#eventDescriptionView').text(event.description || 'No description');
    $('#eventSchool').text(event.school_name || event.title || '');
    $('#eventClass').text(event.class_name || '');
    $('#eventStart').text(`${event.starting_time ? event.starting_time.slice(0, 5) : ''}`);
    $('#eventEnd').text(`${event.ending_time ? event.ending_time.slice(0, 5) : ''}`);
    $('#eventRecurrenceSection').toggle(event.recurrence_type !== 'does_not_repeat');

    // Set minimum dates for editing
    const today = this.formatDate(new Date());
    $('#eventDate').attr('min', today);
    $('#eventEndDate').attr('min', today);
    this.generateTimeOptions($('#eventStartTime'), $('#eventDate').val() || today);
    this.generateTimeOptions($('#eventEndTime'), $('#eventDate').val() || today);

    $('#eventDate').off('change.timeOptions').on('change.timeOptions', () => {
        const selectedDate = $('#eventDate').val();
        this.generateTimeOptions($('#eventStartTime'), selectedDate);
        this.generateTimeOptions($('#eventEndTime'), selectedDate);
        $('#recurrenceStartDatePopup').val(selectedDate);
    });

    // Handle recurrence display
    if (event.recurrence_type !== 'does_not_repeat') {
        let recurrenceText = event.recurrence_type.charAt(0).toUpperCase() + event.recurrence_type.slice(1);
        if (event.recurrence_type === 'weekly' && event.custom_recurrence) {
            try {
                const days = JSON.parse(event.custom_recurrence);
                if (Array.isArray(days) && days.length > 0) {
                    recurrenceText += ` on ${days.join(', ')}`;
                }
            } catch (e) {
                console.error('showEventDetails: Error parsing custom_recurrence:', e);
            }
        }
        let endDateText = event.recurrence_end_date
            ? ` until ${event.recurrence_end_date}`
            : ` until ${this.formatDate(new Date(new Date(event.starting_date).setFullYear(new Date(event.starting_date).getFullYear() + 1)))}`;
        $('#eventRecurrence').text(recurrenceText + endDateText);
    } else {
        $('#eventRecurrence').text('');
    }

    // Handle visio meeting state
    $('#eventParticipantsSection').toggle(isVisio);
    $('#joinMeetingBtn').toggle(isVisio).prop('disabled', isExpired);
    $('#editEventBtn').prop('disabled', isExpired);
    $('#deleteevent').prop('disabled', false);

    if (isExpired) {
        $('#joinMeetingBtn, #editEventBtn').addClass('disabled');
        $('#deleteevent').removeClass('disabled');
        $('#participantCount').text('0');
        $('#joinMeetingBtn').text('<?php echo get_phrase('Start Meeting'); ?>');
    } else if (isVisio && occurrenceData.meeting_id) {
        // Initialize UI with cached data immediately
        $('#participantCount').text(occurrenceData.participant_count || 0);
        $('#joinMeetingBtn').text(occurrenceData.is_running ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Start Meeting'); ?>').prop('disabled', false);

        // Subscribe and request current state
        const meetingID = occurrenceData.meeting_id;
        if (this.socket.connected) {
            this.socket.emit('subscribe', { meetingID });
            this.socket.emit('request_current_state', { meetingID });
        } else {
            console.warn(`Socket.IO not connected for meetingID: ${meetingID}`);
            this.showNotification('warning', 'Unable to connect to real-time updates.');
        }

        // Create a promise to wait for current_state response
        const waitForState = new Promise((resolve) => {
            const handler = (data) => {
                if (data.meetingID === meetingID) {
                    this.socket.off('current_state', handler); // Remove handler after receiving response
                    resolve(data);
                }
            };
            this.socket.on('current_state', handler);

            // Timeout to handle cases where response is not received
            setTimeout(() => {
                this.socket.off('current_state', handler);
                resolve(null); // Fallback to cache
            }, 5000); // 5-second timeout
        });

        // Update UI after receiving current_state
        waitForState.then((data) => {
            if (data) {
                // Update cache
                this.eventCache[eventId].occurrences[occurrenceDate] = {
                    ...this.eventCache[eventId].occurrences[occurrenceDate],
                    participant_count: data.participantCount || 0,
                    is_running: data.isRunning || false
                };
                // Update UI only if modal is still open for this event
                if ($('#eventId').val() === eventId && $('#eventEditModal').hasClass('show')) {
                    $('#participantCount').text(data.participantCount || 0);
                    $('#joinMeetingBtn').text(data.isRunning ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Start Meeting'); ?>').prop('disabled', false);
                }
            }
        });
    } else {
        $('#participantCount').text('0');
        $('#joinMeetingBtn').text('<?php echo get_phrase('Start Meeting'); ?>').prop('disabled', false);
    }

    // Unsubscribe when modal is closed (optional, commented for testing)
    $('#eventEditModal').off('hidden.bs.modal').on('hidden.bs.modal', () => {
        if (isVisio && !isExpired && occurrenceData.meeting_id) {
            this.socket.emit('unsubscribe', { meetingID: occurrenceData.meeting_id });
        }
    });

    // Load classes for editing
    if (event.school_id) {
        $.ajax({
            url: '<?php echo site_url('admin/get_classes_by_school'); ?>',
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


    validateForm(formId) {
        const form = $(`#${formId}`);
        const requiredFields = form.find('[required]');
        let isValid = true;
        requiredFields.each(function() {
            const field = $(this);
            if (!field.val().trim()) {
                field.addClass('is-invalid');
                isValid = false;
            } else {
                field.removeClass('is-invalid');
            }
        });

        const startDate = form.find('[name="start"]').val();
    const endDate = form.find('[name="end_date"]').val() || startDate;
    const startTime = form.find('[name="start_time"]').val();
    const endTime = form.find('[name="end_time"]').val();

    if (startDate && endDate && startTime && endTime && startDate === endDate) {
        const startTimeMinutes = parseInt(startTime.split(':')[0]) * 60 + parseInt(startTime.split(':')[1]);
        const endTimeMinutes = parseInt(endTime.split(':')[0]) * 60 + parseInt(endTime.split(':')[1]);
        if (endTimeMinutes <= startTimeMinutes) {
            this.showNotification('error', 'End time must be after start time');
            form.find('[name="end_time"]').addClass('is-invalid');
            isValid = false;
        } else {
            form.find('[name="end_time"]').removeClass('is-invalid');
        }
    }

        return isValid;
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

    updateRecurrenceType() {
        const allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        const selectedDaysCount = this.selectedDays.length;
        if (selectedDaysCount === allDays.length) {
            $('#recurrenceTypePopup').val('daily');
            $('#daySelection').show();
            $('.day-btn').addClass('active');
        } else if (selectedDaysCount > 0) {
            $('#recurrenceTypePopup').val('weekly');
            $('#daySelection').show();
        } else {
            $('#recurrenceTypePopup').val('does_not_repeat');
            $('#daySelection').hide();
        }
    },

    resetRecurrenceModal() {
        $('#recurrenceForm')[0].reset();
        $('#recurrenceTypePopup').val('does_not_repeat');
        $('#recurrenceEndDatePopup').val('');
        $('#customRecurrencePopup').val('');
        this.selectedDays = [];
        $('.day-btn').removeClass('active');
        $('#daySelection').hide();
    },

    bindGlobalEvents() {
        $('#createEventModal').on('show.bs.modal', () => {
            $('#createeventEndDate').on('change', () => {
        const startDate = $('#createeventDate').val();
        const endDate = $('#createeventEndDate').val();
        if (startDate && endDate && endDate < startDate) {
            this.showNotification('error', 'End date must be on or after start date');
            $('#createeventEndDate').val('');
        }
    });

    // Définir la date minimum à aujourd'hui
    const today = this.formatDate(new Date());
    $('#createeventDate').attr('min', today);
    $('#createeventEndDate').attr('min', today);

    // Générer les options d'heure pour la date sélectionnée
    const selectedDate = $('#createeventDate').val() || today;
    this.generateTimeOptions($('#createeventStartTime'), selectedDate);
    this.generateTimeOptions($('#createeventEndTime'), selectedDate);

    // Mettre à jour les options d'heure lorsque la date change
    $('#createeventDate').off('change.timeOptions').on('change.timeOptions', () => {
        const selectedDate = $('#createeventDate').val();
        this.generateTimeOptions($('#createeventStartTime'), selectedDate);
        this.generateTimeOptions($('#createeventEndTime'), selectedDate);
        $('#recurrenceStartDatePopup').val(selectedDate);
    });
    this.resetRecurrenceModal();
    $.ajax({
        url: '<?php echo site_url('admin/get_user_school'); ?>',
        type: 'GET',
        data: { [csrfName]: csrfHash },
        success: (response) => {
            try {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    const schoolSelect = $('#createSchoolId');
                    schoolSelect.empty();
                    schoolSelect.append(`<option value="${data.data.id}">${this.escapeHtml(data.data.name)}</option>`);
                    schoolSelect.prop('disabled', true);
                    $.ajax({
                        url: '<?php echo site_url('admin/get_classes_by_school'); ?>',
                        type: 'POST',
                        data: { school_id: data.data.id, [csrfName]: csrfHash },
                        success: (response) => {
                            try {
                                const classData = JSON.parse(response);
                                if (classData.status === 'success') {
                                    const classSelect = $('#createClasseId');
                                    classSelect.empty();
                                    classSelect.append('<option value=""><?php echo get_phrase("select_a_class"); ?></option>');
                                    classData.classes.forEach(cls => {
                                        classSelect.append(`<option value="${cls.id}">${this.escapeHtml(cls.name)}</option>`);
                                    });
                                    csrfHash = classData.csrf.csrfHash;

                                    // Synchroniser les dates pour le modal de création
                                    $('#recurrenceModal').on('show.bs.modal', () => {
                                        const eventDate = $('#createeventDate').val();
                                        $('#recurrenceStartDatePopup').val(eventDate || '');
                                        $('#recurrenceStartDateSection').toggle($('#recurrenceTypePopup').val() === 'monthly' || $('#recurrenceTypePopup').val() === 'yearly');

                                        // Synchroniser les changements de date du modal de récurrence vers le modal de création
                                        $('#recurrenceStartDatePopup').off('change').on('change', () => {
                                            $('#createeventDate').val($('#recurrenceStartDatePopup').val());
                                        });

                                        // Synchroniser les changements de date du modal de création vers le modal de récurrence
                                        $('#createeventDate').off('change').on('change', () => {
                                            $('#recurrenceStartDatePopup').val($('#createeventDate').val());
                                        });
                                    });
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
});

        $('#createEventForm').on('submit', (e) => {
    e.preventDefault();
    if (!this.validateForm('createEventForm')) {
        this.showNotification('error', 'Please fill all required fields');
        return;
    }
    const formData = {
        title: $('#createeventTitle').val(),
        description: $('#createeventDescription').val(),
        school_id: $('#createSchoolId').val(),
        class_id: $('#createClasseId').val(),
        starting_date: $('#createeventDate').val(),
        starting_time: $('#createeventStartTime').val() + ':00',
        ending_date: $('#createeventEndDate').val(),
        ending_time: $('#createeventEndTime').val() + ':00',
        recurrence_type: $('#createRecurrenceType').val(),
        recurrence_end_date: $('#createRecurrenceEndDate').val(),
        custom_recurrence: $('#createCustomRecurrence').val(),
        visio: $('#createVisio').is(':checked') ? 1 : 0,
        [csrfName]: csrfHash
    };
    $.ajax({
        url: '<?php echo site_url('admin/create_event'); ?>',
        type: 'POST',
        data: formData,
        success: (response) => {
    try {
        const data = JSON.parse(response);
        if (data.status === 'success') {
            $('#createEventModal').modal('hide');
            $('#createEventForm')[0].reset();
            this.resetRecurrenceModal();
            this.showNotification('success', data.message);
            this.clearEventCache(); // Clear entire cache
            this.calendar.refetchEvents();
            this.calendar.render();
        } else {
            this.showNotification('error', data.message);
        }
        csrfHash = data.csrf.csrfHash;
    } catch (e) {
        this.showNotification('error', 'Invalid server response');
    }
},
        error: () => {
            this.showNotification('error', 'Failed to create event');
        }
    });


});

$('#eventForm').on('submit', (e) => {
    e.preventDefault();
    if (!this.validateForm('eventForm')) {
        this.showNotification('error', 'Please fill all required fields');
        return;
    }
    const formData = {
        id: $('#eventId').val(),
        title: $('#eventTitleInput').val(),
        description: $('#eventDescription').val(),
        school_id: $('#school_id').val(),
        class_id: $('#classe_id').val(),
        starting_date: $('#eventDate').val(),
        starting_time: $('#eventStartTime').val() + ':00',
        ending_date: $('#eventEndDate').val(),
        ending_time: $('#eventEndTime').val() + ':00',
        recurrence_type: $('#recurrenceType').val(),
        recurrence_end_date: $('#recurrenceEndDate').val(),
        custom_recurrence: $('#customRecurrence').val(),
        visio: $('#visio').is(':checked') ? 1 : 0,
        [csrfName]: csrfHash
    };
    $.ajax({
        url: '<?php echo site_url('admin/update_event'); ?>',
        type: 'POST',
        data: formData,
        success: (response) => {
            try {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    // Mettre à jour le cache avec les nouvelles données
                    this.eventCache[formData.id] = {
                        id: formData.id,
                        title: formData.title,
                        description: formData.description,
                        school_id: formData.school_id,
                        class_id: formData.class_id,
                        starting_date: formData.starting_date,
                        starting_time: formData.starting_time,
                        ending_date: formData.ending_date,
                        ending_time: formData.ending_time,
                        recurrence_type: formData.recurrence_type,
                        recurrence_end_date: formData.recurrence_end_date,
                        custom_recurrence: formData.custom_recurrence,
                        visio: formData.visio,
                        school_name: $('#school_id option:selected').text() || this.eventCache[formData.id]?.school_name || '',
                        class_name: $('#classe_id option:selected').text() || this.eventCache[formData.id]?.class_name || '',
                        is_expired: this.eventCache[formData.id]?.is_expired || false
                    };
                    $('#eventEditModal').modal('hide');
                    this.resetRecurrenceModal();
                    this.showNotification('success', data.message);
                    this.clearEventCache(); // Vider le cache
                    this.calendar.refetchEvents(); // Recharger les événements
                    this.calendar.render(); // Forcer le rendu
                } else {
                    this.showNotification('error', data.message);
                }
                csrfHash = data.csrf.csrfHash;
            } catch (e) {
                this.showNotification('error', 'Invalid server response');
            }
        },
        error: () => {
            this.showNotification('error', 'Failed to update event');
        }
    });
});

        $('#deleteevent').on('click', () => {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    const eventId = $('#eventId').val();
                    $.ajax({
                        url: '<?php echo site_url('admin/delete_event'); ?>',
                        type: 'POST',
                        data: { id: eventId, [csrfName]: csrfHash },
                        success: (response) => {
    try {
        const data = JSON.parse(response);
        if (data.status === 'success') {
            const eventId = $('#eventId').val();
            $('#eventEditModal').modal('hide');
            this.resetRecurrenceModal();
            this.showNotification('success', data.message);
            this.clearEventCache(eventId); // Clear only the updated event
            this.calendar.refetchEvents();
            this.calendar.render();
        } else {
            this.showNotification('error', data.message);
        }
        csrfHash = data.csrf.csrfHash;
    } catch (e) {
        this.showNotification('error', 'Invalid server response');
    }
},
                        error: () => {
                            this.showNotification('error', 'Failed to delete event');
                        }
                    });
                }
            });
        });

        $('#editEventBtn'). exch
        $('#cancelEditBtn').on('click', () => {
            $('#eventForm').hide();
            $('#eventDetailsView').show();
        });
        $('#editEventBtn').on('click', () => {
            $('#eventDetailsView').hide();
            $('#eventForm').show();
        });

        $('#classFilter').on('change', () => {
            this.selectedClass = $('#classFilter').val();
            this.calendar.refetchEvents();
        });

        $('#viewFilter').on('change', () => {
            this.currentView = $('#viewFilter').val();
            this.calendar.changeView(this.currentView);
        });

        $('.day-btn').on('click', (e) => {
            const day = $(e.currentTarget).data('day');
            if ($(e.currentTarget).hasClass('active')) {
                $(e.currentTarget).removeClass('active');
                this.selectedDays = this.selectedDays.filter(d => d !== day);
            } else {
                $(e.currentTarget).addClass('active');
                this.selectedDays.push(day);
            }
            $('#customRecurrencePopup').val(JSON.stringify(this.selectedDays));
            this.updateRecurrenceType();
        });

        $('#recurrenceTypePopup').on('change', () => {
    const recurrenceType = $('#recurrenceTypePopup').val();
    const allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    
    // Show/hide start date section for monthly/yearly
    $('#recurrenceStartDateSection').toggle(recurrenceType === 'monthly' || recurrenceType === 'yearly');
    
    if (recurrenceType === 'daily') {
        this.selectedDays = [...allDays];
        $('.day-btn').addClass('active');
        $('#customRecurrencePopup').val(JSON.stringify(this.selectedDays));
        $('#daySelection').show();
    } else if (recurrenceType === 'weekly') {
        $('#daySelection').show();
    } else {
        this.selectedDays = [];
        $('.day-btn').removeClass('active');
        $('#customRecurrencePopup').val('');
        $('#daySelection').hide();
    }

    // Synchroniser la date de début si le modal de récurrence est ouvert pour monthly/yearly
    const targetForm = $('#createEventModal').hasClass('show') ? $('#createEventForm') : $('#eventForm');
    const eventDate = targetForm.find('[name="start"]').val();
    if (recurrenceType === 'monthly' || recurrenceType === 'yearly') {
        $('#recurrenceStartDatePopup').val(eventDate || '');
    }
});

        $('#saveRecurrence').on('click', () => {
    const recurrenceType = $('#recurrenceTypePopup').val();
    const recurrenceEndDate = $('#recurrenceEndDatePopup').val();
    const customRecurrence = $('#customRecurrencePopup').val();

    // Déterminer quel formulaire parent est actif (création ou édition)
    const targetForm = $('#createEventModal').hasClass('show') ? $('#createEventForm') : $('#eventForm');

    // Mettre à jour les champs cachés du formulaire parent
    targetForm.find('[name="recurrence_type"]').val(recurrenceType);
    targetForm.find('[name="recurrence_end_date"]').val(recurrenceEndDate);
    targetForm.find('[name="custom_recurrence"]').val(customRecurrence);

    // Fermer le modal de récurrence
    $('#recurrenceModal').modal('hide');

    // Ne pas réinitialiser immédiatement pour préserver les valeurs
});


        $('#school_id').on('change', () => {
            const schoolId = $('#school_id').val();
            $.ajax({
                url: '<?php echo site_url('admin/get_classes_by_school'); ?>',
                type: 'POST',
                data: { school_id: schoolId, [csrfName]: csrfHash },
                success: (response) => {
                    try {
                        const data = JSON.parse(response);
                        if (data.status === 'success') {
                            const classSelect = $('#classe_id');
                            classSelect.empty();
                            classSelect.append('<option value=""><?php echo get_phrase("select_a_class"); ?></option>');
                            data.classes.forEach(cls => {
                                classSelect.append(`<option value="${cls.id}">${this.escapeHtml(cls.name)}</option>`);
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

updateParticipantUI(eventId, participantCount, isRunning) {
    if ($('#eventId').val() === eventId && $('#eventEditModal').hasClass('show')) {
        $('#participantCount').text(participantCount || 0);
        $('#joinMeetingBtn').text(isRunning ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Start Meeting'); ?>');
    }
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
            url: '<?php echo site_url('admin/get_event'); ?>',
            type: 'GET',
            data: {
                id: eventId,
                [csrfName]: csrfHash
            },
            async: true,
            success: (response) => {
                try {
                    const data = JSON.parse(response);
                    if (data.status === 'success' && data.data) {
                        event = data.data;
                        const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
                        event.is_expired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);
                        this.eventCache[eventId] = event;
                        csrfHash = data.csrf.csrfHash;
                    } else {
                        console.error(`Failed to fetch event ${eventId}:`, data.message);
                        this.showNotification('error', data.message || 'Failed to load event');
                    }
                } catch (e) {
                    console.error(`Error parsing get_event response for eventId ${eventId}:`, e, response);
                    this.showNotification('error', 'Invalid server response');
                }
            },
            error: (xhr) => {
                console.error(`AJAX error fetching event ${eventId}:`, xhr.status, xhr.statusText);
                this.showNotification('error', 'Failed to load event');
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
            url: '<?php echo site_url('admin/start_meeting'); ?>',
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
                            participant_count: 0,
                            is_running: data.is_running || false
                        };
                        $('#participantCount').text(0);
                        $('#joinMeetingBtn').text(data.is_running ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Start Meeting'); ?>');

                        if (this.socket.connected) {
                            this.socket.emit('subscribe', { meetingID: data.meeting_id });
                            this.socket.emit('request_current_state', { meetingID: data.meeting_id });
                        } else {
                            console.warn(`Socket.IO not connected for meetingID: ${data.meeting_id}`);
                            this.showNotification('warning', 'Unable to connect to real-time updates.');
                        }

                        const joinUrl = data.join_url || '<?php echo site_url('bigbluebutton/join_meeting'); ?>/' + encodeURIComponent(data.meeting_id);

                        const newWindow = window.open(joinUrl, '_blank');
                        if (!newWindow || newWindow.closed || typeof newWindow.closed === 'undefined') {
                            this.showNotification('warning', 'Unable to open meeting. Please allow pop-ups for this site or click <a href="' + joinUrl + '" target="_blank">here</a> to join.', 5000);
                        } else {
                            this.showNotification('success', 'Starting meeting...');
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

        if (this.socket.connected) {
            this.socket.emit('subscribe', { meetingID: occurrenceData.meeting_id });
            this.socket.emit('request_current_state', { meetingID: occurrenceData.meeting_id });
        } else {
            console.warn(`Socket.IO not connected for meetingID: ${occurrenceData.meeting_id}`);
            this.showNotification('warning', 'Unable to connect to real-time updates.');
        }

        const joinUrl = '<?php echo site_url('bigbluebutton/join_meeting'); ?>/' + encodeURIComponent(occurrenceData.meeting_id);

        const newWindow = window.open(joinUrl, '_blank');
        if (!newWindow || newWindow.closed || typeof newWindow.closed === 'undefined') {
            this.showNotification('warning', 'Unable to open meeting. Please allow pop-ups for this site or click <a href="' + joinUrl + '" target="_blank">here</a> to join.', 5000);
        } else {
            this.showNotification('success', 'Joining meeting...');
        }
    }
},
};

$(document).ready(() => {
    // Load FullCalendar dependencies
    const loadScripts = async () => {
    const scripts = [
        'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js',
        'https://cdn.jsdelivr.net/npm/sweetalert2@11',
        'https://cdn.jsdelivr.net/npm/socket.io-client@4.7.5/dist/socket.io.min.js'
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

    $('#editEventBtn').on('click', () => {
        $('#eventEditModalLabel').text('<?php echo get_phrase("Edit_event"); ?>');
        $('#eventDetailsView').hide();
        $('#eventForm').show();
    });

    // Reset modal title when cancel button is clicked
    $('#cancelEditBtn').on('click', () => {
        $('#eventEditModalLabel').text('<?php echo get_phrase("event_details"); ?>');
        $('#eventForm').hide();
        $('#eventDetailsView').show();
    });

    // Reset modal title when modal is closed
    $('#eventEditModal').on('hidden.bs.modal', () => {
        $('#eventEditModalLabel').text('<?php echo get_phrase("event_details"); ?>');
    });
});
</script>