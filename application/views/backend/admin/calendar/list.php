
<style>
    /* Participants Picker - Modern Chip Input */
    .pp-picker {
        position: relative;
    }
    .pp-field {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 5px;
        min-height: 42px;
        padding: 6px 10px;
        border: 1px solid #ced4da;
        border-radius: 8px;
        background: #fff;
        cursor: text;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .pp-field:hover {
        border-color: #adb5bd;
    }
    .pp-field.pp-focus {
        border-color: var(--exp-primary, #6366f1);
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.12);
    }
    .pp-field .pp-chip {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        background: var(--exp-primary, #6366f1);
        color: #fff;
        border-radius: 16px;
        padding: 2px 6px 2px 10px;
        font-size: 0.8rem;
        font-weight: 500;
        white-space: nowrap;
        max-width: 200px;
        animation: ppChipIn 0.15s ease;
    }
    @keyframes ppChipIn {
        from { transform: scale(0.85); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }
    .pp-field .pp-chip .pp-chip-text {
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .pp-field .pp-chip .pp-chip-x {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: rgba(255,255,255,0.25);
        cursor: pointer;
        font-size: 0.75rem;
        line-height: 1;
        transition: background 0.15s;
        flex-shrink: 0;
    }
    .pp-field .pp-chip .pp-chip-x:hover {
        background: rgba(255,255,255,0.45);
    }
    .pp-field .pp-input {
        flex: 1;
        min-width: 100px;
        border: none;
        outline: none;
        font-size: 0.88rem;
        background: transparent;
        padding: 2px 0;
    }
    .pp-field .pp-input::placeholder {
        color: #adb5bd;
    }
    .pp-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        z-index: 1060;
        background: #fff;
        border: 1px solid #dee2e6;
        border-top: none;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        max-height: 260px;
        overflow-y: auto;
        display: none;
    }
    .pp-picker.pp-open .pp-dropdown {
        display: block;
    }
    .pp-picker.pp-open .pp-field {
        border-radius: 8px 8px 0 0;
        border-bottom-color: #dee2e6;
    }
    .pp-dropdown .pp-group {
        padding: 8px 14px 4px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        color: #8b8fa3;
        user-select: none;
    }
    .pp-dropdown .pp-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 8px 14px;
        cursor: pointer;
        transition: background 0.1s;
        font-size: 0.88rem;
        color: #333;
    }
    .pp-dropdown .pp-item:hover {
        background: #f1f3f9;
    }
    .pp-dropdown .pp-item.pp-checked {
        background: rgba(99, 102, 241, 0.06);
    }
    .pp-dropdown .pp-item .pp-check-icon {
        width: 20px;
        height: 20px;
        border-radius: 5px;
        border: 2px solid #ced4da;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .pp-dropdown .pp-item.pp-checked .pp-check-icon {
        background: var(--exp-primary, #6366f1);
        border-color: var(--exp-primary, #6366f1);
    }
    .pp-dropdown .pp-item.pp-checked .pp-check-icon::after {
        content: '';
        width: 5px;
        height: 9px;
        border: solid #fff;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg) translateY(-1px);
    }
    .pp-dropdown .pp-item .pp-label {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .pp-dropdown .pp-item .pp-role {
        font-size: 0.75rem;
        color: #8b8fa3;
        background: #f1f3f9;
        padding: 1px 7px;
        border-radius: 10px;
        flex-shrink: 0;
    }
    .pp-dropdown .pp-empty {
        padding: 20px 14px;
        text-align: center;
        color: #adb5bd;
        font-size: 0.88rem;
    }
    .pp-dropdown::-webkit-scrollbar {
        width: 6px;
    }
    .pp-dropdown::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }

    /* Modern Buttons */
    .modern-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.6rem 1.2rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        gap: 0.5rem;
        cursor: pointer;
        min-width: 100px;
    }

    .modern-btn i {
        font-size: 1.1rem;
    }

    .modern-btn-primary {
        background: var(--exp-primary, #6366f1);
        color: white;
        box-shadow: 0 2px 4px rgba(99, 102, 241, 0.2);
    }
    .modern-btn-primary:hover {
        background: var(--exp-primary-dark, #4338ca);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(99, 102, 241, 0.3);
    }

    .modern-btn-danger {
        background: #fee2e2;
        color: #ef4444;
        border-color: #fecaca;
    }
    .modern-btn-danger:hover {
        background: #fecaca;
        color: #dc2626;
        transform: translateY(-2px);
        box-shadow: 0 2px 4px rgba(239, 68, 68, 0.1);
    }

    .modern-btn-success {
        background: #dcfce7;
        color: #16a34a;
        border-color: #bbf7d0;
    }
    .modern-btn-success:hover {
        background: #bbf7d0;
        color: #15803d;
        transform: translateY(-2px);
        box-shadow: 0 2px 4px rgba(22, 163, 74, 0.1);
    }

    .modern-btn-secondary {
        background: #f1f5f9;
        color: #64748b;
        border-color: #e2e8f0;
    }
    .modern-btn-secondary:hover {
        background: #e2e8f0;
        color: #475569;
        transform: translateY(-2px);
    }
</style>

    <div class="exp-toolbar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
        <div class="exp-toolbar-left" style="display: flex; gap: 0.5rem; align-items: center;">
            <button class="exp-btn" style="padding: 0.5rem 1rem; background: var(--exp-light); border: 1px solid var(--exp-border);" onclick="CalendarApp.goToToday()"><?php echo get_phrase('TODAY'); ?></button>
            <button class="exp-btn" style="padding: 0.5rem; width: 40px; justify-content: center; background: var(--exp-light); border: 1px solid var(--exp-border);" onclick="CalendarApp.previousPeriod()">
                <i class="mdi mdi-chevron-left" style="font-size: 20px;"></i>
            </button>
            <button class="exp-btn" style="padding: 0.5rem; width: 40px; justify-content: center; background: var(--exp-light); border: 1px solid var(--exp-border);" onclick="CalendarApp.nextPeriod()">
                <i class="mdi mdi-chevron-right" style="font-size: 20px;"></i>
            </button>
            <h2 class="month-nav mb-0" id="monthYear" style="margin-left: 1rem; font-size: 1.5rem; font-weight: 700; color: var(--exp-dark);"></h2>
        </div>
        <div class="exp-toolbar-right" style="display: flex; gap: 1rem; align-items: center;">
             <select class="form-control" id="viewFilter" style="width: auto; border-radius: 10px; border: 1px solid var(--exp-border); padding: 0.5rem 2rem 0.5rem 1rem;">
                <option value="dayGridMonth"><?php echo get_phrase('Month'); ?></option>
                <option value="timeGridWeek"><?php echo get_phrase('Week'); ?></option>
                <option value="timeGridDay"><?php echo get_phrase('Day'); ?></option>
                <option value="listMonth"><?php echo get_phrase('List'); ?></option>
            </select>
        </div>
    </div>
    <!-- FullCalendar container -->
    <div id="calendar"></div>
    <div class="modal fade" id="createEventModal" tabindex="-1" role="dialog" aria-labelledby="createEventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content exp-modal-content">
                <div class="modal-header exp-modal-header">
                    <h5 class="modal-title" id="createEventModalLabel"><?php echo get_phrase('New_Event'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-calendar">
                    <form id="createEventForm">
                        <input type="hidden" id="createRecurrenceType" name="recurrence_type" value="does_not_repeat">
                        <input type="hidden" id="createRecurrenceEndDate" name="recurrence_end_date">
                        <input type="hidden" id="createCustomRecurrence" name="custom_recurrence">
                        
                        <div class="exp-form-group">
                            <label for="createeventTitle" class="exp-form-label"><span class="mdi mdi-format-title"></span> <?php echo get_phrase('Title'); ?> <span class="required text-danger">*</span></label>
                            <input type="text" class="exp-form-control" id="createeventTitle" name="title" placeholder="<?php echo get_phrase('enter_title'); ?>" required>
                        </div>

                        <div class="exp-form-group">
                            <label for="createeventDescription" class="exp-form-label"><span class="mdi mdi-text"></span> <?php echo get_phrase('Description'); ?></label>
                            <textarea class="exp-form-control" id="createeventDescription" name="description" rows="3" placeholder="<?php echo get_phrase('enter_description'); ?>"></textarea>
                        </div>

                        <div class="exp-form-group">
                            <label class="exp-form-label"><span class="mdi mdi-account-multiple"></span> <?php echo get_phrase('participants'); ?> <span class="required text-danger">*</span></label>
                            
                            <div hidden class="mb-2">
                                <select class="exp-form-control" id="createSchoolId" name="school_id" required></select>
                            </div>
                            
                            <div class="pp-picker" id="createPPicker">
                                <div class="pp-field" id="createPPField">
                                    <input type="text" class="pp-input" id="participantsSearchInput" placeholder="<?php echo get_phrase('invite_attendees'); ?>" autocomplete="off">
                                </div>
                                <div class="pp-dropdown" id="participantsDropdown"></div>
                            </div>
                            <input type="hidden" name="participants" id="participantsInput">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="exp-form-group">
                                    <label for="createeventDate" class="exp-form-label"><span class="mdi mdi-calendar"></span> <?php echo get_phrase('start_date'); ?> <span class="required text-danger">*</span></label>
                                    <input type="date" class="exp-form-control" id="createeventDate" name="start" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="exp-form-group">
                                    <label for="createeventStartTime" class="exp-form-label"><span class="mdi mdi-clock-outline"></span> <?php echo get_phrase('start_time'); ?> <span class="required text-danger">*</span></label>
                                    <select class="exp-form-control" id="createeventStartTime" name="start_time" required>
                                        <option value=""><?php echo get_phrase('select_time'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="exp-form-group">
                                    <label for="createeventEndDate" class="exp-form-label"><span class="mdi mdi-calendar"></span> <?php echo get_phrase('end_date'); ?></label>
                                    <input type="date" class="exp-form-control" id="createeventEndDate" name="end_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="exp-form-group">
                                    <label for="createeventEndTime" class="exp-form-label"><span class="mdi mdi-clock-outline"></span> <?php echo get_phrase('end_time'); ?> <span class="required text-danger">*</span></label>
                                    <select class="exp-form-control" id="createeventEndTime" name="end_time" required>
                                        <option value=""><?php echo get_phrase('select_time'); ?></option>
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

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <button type="button" class="exp-btn" style="background: var(--exp-light); border: 1px solid var(--exp-border);" data-bs-toggle="modal" data-bs-target="#recurrenceModal">
                                <i class="mdi mdi-repeat"></i> <?php echo get_phrase('repeat'); ?>
                            </button>

                            <div class="d-flex align-items-center gap-2">
                                <label for="createVisio" class="mb-0" style="font-weight: 600; color: var(--exp-dark);"><?php echo get_phrase('visio_conference'); ?></label>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="createVisio" name="visio">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <button type="submit" class="modern-btn modern-btn-primary">
                                <i class="mdi mdi-check"></i> <?php echo get_phrase('save_event'); ?>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="eventEditModal" tabindex="-1" role="dialog" aria-labelledby="eventEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content exp-modal-content">
                <div class="modal-header exp-modal-header">
                    <h5 class="modal-title" id="eventEditModalLabel"><?php echo get_phrase('event_details'); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="eventDetailsView" style="display: none; padding: 1rem;">
                      <div class="mb-3">
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;"><?php echo get_phrase('Created By'); ?></h6>
                            <p id="eventCreator" class="mb-0 fw-bold"></p>
                        </div>
                        <div class="mb-3">
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;"><?php echo get_phrase('Title'); ?></h6>
                            <p id="eventTitle" class="mb-0 fw-bold"></p>
                        </div>
                        <div class="mb-3">
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;"><?php echo get_phrase('Description'); ?></h6>
                            <p id="eventDescriptionView" class="mb-0"></p>
                        </div>
                        <div class="mb-3">
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;"><?php echo get_phrase('Community'); ?></h6>
                            <p id="eventSchool" class="mb-0"></p>
                        </div>
                        <div class="mb-3">
                           <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;"><?php echo get_phrase('Participants'); ?></h6>
                            <div id="eventParticipants" class="badges-container"></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;"><?php echo get_phrase('From'); ?></h6>
                                <p id="eventStart" class="mb-0"></p>
                            </div>
                            <div class="col-6">
                                <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;"><?php echo get_phrase('To'); ?></h6>
                                <p id="eventEnd" class="mb-0"></p>
                            </div>
                        </div>
                        <div class="mb-3" id="eventRecurrenceSection" style="display: none;">
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;"><?php echo get_phrase('Recurrence'); ?></h6>
                            <p id="eventRecurrence" class="mb-0"></p>
                        </div>
                        <div class="mb-3">
                            <h6 class="text-uppercase text-muted mb-1" style="font-size: 0.75rem; letter-spacing: 0.5px;"><?php echo get_phrase('number of participants'); ?></h6>
                             <span id="participantCount" class="badge bg-light text-dark border">0</span>
                        </div>
                        
                        <div class="d-flex gap-2 mt-4 pt-3 border-top">
                            <button type="button" class="modern-btn modern-btn-primary flex-grow-1" id="editEventBtn">
                                <i class="mdi mdi-pencil"></i> <?php echo get_phrase('Edit') ?>
                            </button>
                            <button type="button" class="modern-btn modern-btn-danger flex-grow-1" id="deleteevent">
                                <i class="mdi mdi-trash-can"></i> <?php echo get_phrase('Delete') ?>
                            </button>
                            <button type="button" class="modern-btn modern-btn-success flex-grow-1" style="display: none;" id="joinMeetingBtn">
                                <i class="mdi mdi-video"></i> <?php echo get_phrase('Start Meeting') ?>
                            </button>
                        </div>
                    </div>
                    
                    <div class="modal-body-calendar-edit">
                    <form id="eventForm" style="display: none;">
                        <input type="hidden" id="eventId" name="id">
                        <input type="hidden" id="recurrenceType" name="recurrence_type" value="does_not_repeat">
                        <input type="hidden" id="recurrenceEndDate" name="recurrence_end_date">
                        <input type="hidden" id="customRecurrence" name="custom_recurrence">
                        
                        <div class="exp-form-group">
                            <label for="eventTitleInput" class="exp-form-label"><span class="mdi mdi-format-title"></span> <?php echo get_phrase('Title') ?> <span class="required text-danger">*</span></label>
                            <input type="text" class="exp-form-control" id="eventTitleInput" name="title" placeholder="<?php echo get_phrase('Title') ?>" required>
                        </div>

                        <div class="exp-form-group">
                            <label for="eventDescription" class="exp-form-label"><span class="mdi mdi-text"></span> <?php echo get_phrase("Description") ?></label>
                            <textarea class="exp-form-control" id="eventDescription" name="description" rows="3" placeholder="<?php echo get_phrase("Description") ?>"></textarea>
                        </div>

                        <div class="exp-form-group">
                            <label class="exp-form-label"><span class="mdi mdi-account-multiple"></span> <?php echo get_phrase('participants'); ?> <span class="required text-danger">*</span></label>
                            
                            <div hidden class="mb-2">
                                <select class="exp-form-control" id="school_id" name="school_id" required></select>
                            </div>
                            
                            <div class="pp-picker" id="editPPicker">
                                <div class="pp-field" id="editPPField">
                                    <input type="text" class="pp-input" id="editParticipantsSearchInput" placeholder="<?php echo get_phrase('Search classes or users'); ?>" autocomplete="off">
                                </div>
                                <div class="pp-dropdown" id="editParticipantsDropdown"></div>
                            </div>
                            <input type="hidden" name="participants" id="editParticipantsInput">
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="exp-form-group">
                                    <label for="eventDate" class="exp-form-label"><span class="mdi mdi-calendar"></span> <?php echo get_phrase('start_date'); ?> <span class="required text-danger">*</span></label>
                                    <input type="date" class="exp-form-control" id="eventDate" name="start" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="exp-form-group">
                                    <label for="eventStartTime" class="exp-form-label"><span class="mdi mdi-clock-outline"></span> <?php echo get_phrase('start_time'); ?> <span class="required text-danger">*</span></label>
                                    <select class="exp-form-control" id="eventStartTime" name="start_time" required>
                                        <option value=""><?php echo get_phrase('start time'); ?></option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="exp-form-group">
                                    <label for="eventEndDate" class="exp-form-label"><span class="mdi mdi-calendar"></span> <?php echo get_phrase('end_date'); ?></label>
                                    <input type="date" class="exp-form-control" id="eventEndDate" name="end_date">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="exp-form-group">
                                    <label for="eventEndTime" class="exp-form-label"><span class="mdi mdi-clock-outline"></span> <?php echo get_phrase('end_time'); ?> <span class="required text-danger">*</span></label>
                                    <select class="exp-form-control" id="eventEndTime" name="end_time" required>
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

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <button type="button" class="modern-btn modern-btn-secondary" data-bs-toggle="modal" data-bs-target="#recurrenceModal">
                                <i class="mdi mdi-repeat"></i> <?php echo get_phrase('Repeat') ?>
                            </button>

                            <div class="d-flex align-items-center gap-2">
                                <label for="visio" class="mb-0" style="font-weight: 600; color: var(--exp-dark);"><?php echo get_phrase('visio_conference'); ?></label>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="visio" name="visio">
                                    <span class="slider"></span>
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                            <button type="button" class="modern-btn modern-btn-secondary" id="cancelEditBtn">
                                <?php echo get_phrase('Cancel') ?>
                            </button>
                            <button type="submit" class="modern-btn modern-btn-primary">
                                <i class="mdi mdi-check"></i> <?php echo get_phrase('Save') ?>
                            </button>
                        </div>
                    </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" style="top:20%; z-index: 1070;" id="recurrenceModal" tabindex="-1" role="dialog" aria-labelledby="recurrenceModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content exp-modal-content">
                <div class="modal-header exp-modal-header">
                    <h5 class="modal-title" id="recurrenceModalLabel"><?php echo get_phrase('repeat') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body modal-body-calendar">
                    <form id="recurrenceForm">
                        <div class="exp-form-group">
                            <label for="recurrenceTypePopup" class="exp-form-label"><span class="mdi mdi-calendar-sync"></span> <?php echo get_phrase('Recurrence Type'); ?></label>
                            <select class="exp-form-control" id="recurrenceTypePopup" name="recurrence_type">
                                <option value="does_not_repeat"><?php echo get_phrase('does_not_repeat'); ?></option>
                                <option value="daily"><?php echo get_phrase('day'); ?></option>
                                <option value="weekly"><?php echo get_phrase('week'); ?></option>
                                <option value="monthly"><?php echo get_phrase('months'); ?></option>
                                <option value="yearly"><?php echo get_phrase('year'); ?></option>
                            </select>
                        </div>
                        <div class="exp-form-group" id="recurrenceStartDateSection" style="display: none;">
                            <label for="recurrenceStartDatePopup" class="exp-form-label"><span class="mdi mdi-calendar-start"></span> <?php echo get_phrase('Start Date'); ?></label>
                            <input type="date" class="exp-form-control" id="recurrenceStartDatePopup" name="recurrence_start_date">
                        </div>
                        <div class="exp-form-group" id="daySelection" style="display: none;">
                            <label class="exp-form-label"><span class="mdi mdi-calendar-week"></span> <?php echo get_phrase('Select Days'); ?></label>
                            <div class="d-flex justify-content-between gap-2 flex-wrap">
                                <button type="button" class="exp-btn exp-btn-outline day-btn" style="padding: 0.5rem; width: 40px; justify-content: center;" data-day="Monday">M</button>
                                <button type="button" class="exp-btn exp-btn-outline day-btn" style="padding: 0.5rem; width: 40px; justify-content: center;" data-day="Tuesday">T</button>
                                <button type="button" class="exp-btn exp-btn-outline day-btn" style="padding: 0.5rem; width: 40px; justify-content: center;" data-day="Wednesday">W</button>
                                <button type="button" class="exp-btn exp-btn-outline day-btn" style="padding: 0.5rem; width: 40px; justify-content: center;" data-day="Thursday">T</button>
                                <button type="button" class="exp-btn exp-btn-outline day-btn" style="padding: 0.5rem; width: 40px; justify-content: center;" data-day="Friday">F</button>
                                <button type="button" class="exp-btn exp-btn-outline day-btn" style="padding: 0.5rem; width: 40px; justify-content: center;" data-day="Saturday">S</button>
                                <button type="button" class="exp-btn exp-btn-outline day-btn" style="padding: 0.5rem; width: 40px; justify-content: center;" data-day="Sunday">S</button>
                            </div>
                        </div>
                        <div class="exp-form-group">
                            <label for="recurrenceEndDatePopup" class="exp-form-label"><span class="mdi mdi-calendar-end"></span> <?php echo get_phrase('End Date'); ?></label>
                            <input type="date" class="exp-form-control" id="recurrenceEndDatePopup" name="recurrence_end_date">
                            <small class="text-muted mt-1 d-block"><?php echo get_phrase('leave_blank_for_default_one_year'); ?></small>
                        </div>
                        
                        <div hidden class="exp-form-group hidden">
                            <label for="customRecurrencePopup" class="exp-form-label"><?php echo get_phrase('Day_selected'); ?></label>
                            <input type="text" class="exp-form-control" id="customRecurrencePopup" name="custom_recurrence" readonly>
                        </div>
                        
                        <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                            <button type="button" class="modern-btn modern-btn-secondary" data-bs-dismiss="modal">
                                <?php echo get_phrase('Cancel') ?>
                            </button>
                            <button type="button" class="modern-btn modern-btn-primary" id="saveRecurrence">
                                <i class="mdi mdi-check"></i> <?php echo get_phrase('save') ?>
                            </button>
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
<script>
const enableToasts = <?php echo json_encode((bool)$this->config->item('enable_toasts')); ?>;

const CalendarApp = {
  calendar: null,
  currentView: 'dayGridMonth',
  selectedClass: '',
  isLoading: false,
  selectedDays: [],
  pollingInterval: null,
  currentEventId: null, // Nouvelle propriété pour suivre l'événement actuel
  currentMeetingId: null, // Nouvelle propriété pour suivre le meetingID actuel
  currentOccurrenceDate: null,
  isPolling: false,
  hasActiveMeetings: false,
  activeMeetings: new Set(),
  activeMeetingsPollingInterval: null,
  lastEventId: null,
  lastMeetingId: null,
  lastOccurrenceDate: null,
  lastHasActiveMeetings: false,

  handleDateClick(info) {
    let dateStr = info.dateStr || info.startStr;
    if (dateStr && dateStr.includes('T')) {
        dateStr = dateStr.split('T')[0];
    }
    
    $('#createEventForm')[0].reset();
    $('#createeventDate').val(dateStr);
    
    // Initialize time options for the selected date
    this.generateTimeOptions($('#createeventStartTime'), dateStr);
    this.generateTimeOptions($('#createeventEndTime'), dateStr);
    
    $('#createEventModal').modal('show');
  },

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
    this.initCalendar();
    this.bindGlobalEvents();
    this.loadClasses();
    
    this.pollActiveMeetings();
    this.setupResizeListener();

    $('#eventEditModal, #createEventModal, #recurrenceModal').on('show.bs.modal', () => {
      this.closeAllPopovers();
    });

    $('#eventEditModal, #createEventModal, #recurrenceModal').on('hidden.bs.modal', () => {
        setTimeout(() => {
            if (this.calendar) {
                this.calendar.updateSize();
            }
        }, 200);
    });
    
    document.addEventListener('visibilitychange', () => {
  if (document.visibilityState === 'visible') {
    // Restaurer le polling pour un événement spécifique
    if (this.lastEventId && this.lastMeetingId && this.lastOccurrenceDate) {
      this.startPolling(this.lastEventId, this.lastMeetingId, this.lastOccurrenceDate);
    }
    // Force check for active meetings (even if lastHasActiveMeetings is false) to detect new ones started while hidden
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
        locale: navigator.language,
        headerToolbar: false,
        lazyFetching: true, // Activer le lazy loading
        views: {
            timeGridDay: { type: 'timeGrid', duration: { days: 1 } },
            listMonth: { type: 'list', duration: { months: 1 } }
        },
        events: (info, successCallback, failureCallback) => {
            this.loadEvents(info.startStr, info.endStr, successCallback, failureCallback);
        },
        selectable: true,
        select: (info) => {
            this.handleDateClick(info);
        },
        dateClick: (info) => {
            this.handleDateClick(info);
        },
        eventClick: (info) => {
            info.jsEvent.preventDefault();
            this.closeAllPopovers();
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
                    delay: { show: 300, hide: 150 },
                }).popover('show');
            } catch (e) {
                console.warn('Error initializing popover:', e);
            }
        },

        eventMouseLeave: (info) => {
          if (info.el && $(info.el).data('bs.popover')) {
            try {
              $(info.el).popover('dispose');
            } catch (e) {
              console.warn('Error disposing popover on mouseleave:', e);
            }
          }
          $('.popover').remove();
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
            this.loadClassesWithEvents(info.start, info.end);
            this.checkAndRestoreActiveMeetings();
            this.pollActiveMeetings();
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

  generateTimeOptions(selectElement, selectedDate, isEditMode = false) {
  const now = new Date();
  const today = this.formatDate(now);
  const isToday = selectedDate === today;
  const currentHour = now.getHours();
  const currentMinute = now.getMinutes();

  selectElement.find('option:not(:first)').remove();
  const generatedOptions = [];
  for (let h = 0; h < 24; h++) {
    for (let m = 0; m < 60; m += 15) {
      // Skip past times only for creation mode on today's date
      if (!isEditMode && isToday && (h < currentHour || (h === currentHour && m <= currentMinute))) {
        continue;
      }
      const time = `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
      selectElement.append(`<option value="${time}">${time}</option>`);
      generatedOptions.push(time);
    }
  }

  // Ensure the selected value is still valid after regenerating options
  const currentValue = selectElement.val();
  if (currentValue && !generatedOptions.includes(currentValue)) {
    selectElement.val('');
  }
},

  formatDate(date) {
    if (!(date instanceof Date) || isNaN(date)) return '';
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
  },

  loadEvents(start, end, successCallback, failureCallback) {
    if (this.isLoading) return;
        this.isLoading = true;

        const startDate = new Date(start);
        const endDate = new Date(end);
        startDate.setMonth(startDate.getMonth() - 2); // 2 mois avant
        endDate.setMonth(endDate.getMonth() + 2); // 2 mois après

    const adjustedStart = startDate.toISOString().split('T')[0];
    const adjustedEnd = endDate.toISOString().split('T')[0];

        const cacheKey = `events_${start}_${end}_${this.selectedClass}`;
        const cachedEvents = sessionStorage.getItem(cacheKey);

        if (cachedEvents) {
    try {
        const events = JSON.parse(cachedEvents);
        // Preserve isRunning, participant_count, and meeting_id from existing events
        const existingEvents = this.calendar ? this.calendar.getEvents() : [];
        const existingEventMap = new Map();
        existingEvents.forEach(event => {
            existingEventMap.set(event.id, {
                isRunning: event.extendedProps.is_running,
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
                        is_running: existing.isRunning || false,
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
        console.warn('Error parsing cached events:', e);
    }
}

    $.ajax({
            url: '<?php echo site_url('admin/get_events'); ?>',
            type: 'GET',
            data: {
                start_date: adjustedStart,
                end_date: adjustedEnd,
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

                    data.data.forEach(event => {
    // Skip events with missing required fields
    if (!event.starting_date || !event.starting_time || !event.ending_time) {
        return;
    }

                        // Preserve isRunning, participant_count, and meeting_id from existing events
    const existingEvents = this.calendar ? this.calendar.getEvents() : [];
    const existingEventMap = new Map();
    existingEvents.forEach(event => {
        existingEventMap.set(event.id, {
            isRunning: event.extendedProps.is_running,
            participant_count: event.extendedProps.participant_count,
            meeting_id: event.extendedProps.meeting_id
        });
    });

    // Handle recurring events
    if (event.recurrence_type !== 'does_not_repeat' && event.occurrences && Object.keys(event.occurrences).length > 0) {
        Object.keys(event.occurrences).forEach(occurrenceDate => {
            const occurrenceData = event.occurrences[occurrenceDate];
            const uniqueId = `${event.id}_${occurrenceDate}`;
            // Include occurrence if within date range, regardless of is_expired
            if (!uniqueEventIds.has(uniqueId) && occurrenceDate >= start.split('T')[0] && occurrenceDate <= end.split('T')[0]) {
                const existing = existingEventMap.get(uniqueId);
                const occurrenceEndDate = event.ending_date && event.ending_date >= occurrenceDate ? event.ending_date : occurrenceDate;
                events.push({
                    id: uniqueId,
                    title: event.title || 'unknown',
                    start: `${occurrenceDate}T${event.starting_time}`,
                    end: `${occurrenceEndDate}T${event.ending_time}`,
                    extendedProps: {
                        description: event.description || '',
                        school_id: event.school_id,
                        recurrence_type: event.recurrence_type,
                        recurrence_end_date: event.recurrence_end_date || null,
                        custom_recurrence: event.custom_recurrence || null,
                        visio: event.visio == 1,
                        school_name: event.school_name || '',
                        class_name: event.class_name || '',
                        is_expired: occurrenceData.is_expired || false,
                        occurrence_date: occurrenceDate,
                        meeting_id: occurrenceData.meeting_id || (existing ? existing.meeting_id : null),
                        is_running: existing ? existing.isRunning : (occurrenceData.is_running || false),
                        participant_count: existing ? existing.participant_count : (occurrenceData.participant_count || 0),
                        participants: event.participants || []
                    }
                });
                uniqueEventIds.add(uniqueId);
            }
            });
    } else {
        // Handle non-recurring events
        const uniqueId = event.id;
        if (!uniqueEventIds.has(uniqueId) && event.starting_date >= start.split('T')[0] && event.starting_date <= end.split('T')[0]) {
            const existing = existingEventMap.get(uniqueId);
            const endDate = event.ending_date || event.starting_date;
            events.push({
                id: uniqueId,
                title: event.title || 'No title',
                start: `${event.starting_date}T${event.starting_time}`,
                end: `${endDate}T${event.ending_time}`,
                extendedProps: {
                    description: event.description || '',
                    school_id: event.school_id,
                    recurrence_type: event.recurrence_type,
                    recurrence_end_date: event.recurrence_end_date || null,
                    custom_recurrence: event.custom_recurrence || null,
                    visio: event.visio == 1,
                    school_name: event.school_name || '',
                    class_name: event.class_name || '',
                    is_expired: event.is_expired || false,
                    occurrence_date: event.starting_date,
                    meeting_id: event.occurrences && event.occurrences[event.starting_date] ? event.occurrences[event.starting_date].meeting_id : (existing ? existing.meeting_id : null),
                    is_running: existing ? existing.isRunning : (event.occurrences && event.occurrences[event.starting_date] ? event.occurrences[event.starting_date].is_running || false : false),
                    participant_count: existing ? existing.participant_count : (event.occurrences && event.occurrences[event.starting_date] ? event.occurrences[event.starting_date].participant_count || 0 : 0),
                    participants: event.participants || []
                }
            });
            uniqueEventIds.add(uniqueId);
        }
    }
});

                    // Mettre en cache les événements
                        sessionStorage.setItem(cacheKey, JSON.stringify(events));
                        this.calendar.getEvents().forEach(event => event.remove());
                        successCallback(events);
                        csrfHash = data.csrf.csrfHash;
                    } else {
                        this.showNotification('error', data.message || 'Failed to load events');
                        failureCallback();
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                    this.showNotification('error', "Failed to parse events");
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
      error: () => { this.showNotification('error', "Failed to load school"); }
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
         this.showNotification('error', "No existing class.");
        }
      },
      error: () => { this.showNotification('error', "Failed to load school"); }
    });
  },

  showNotification(type, message, duration = 3000) {
    if (!enableToasts) return;
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
    const start = event.starting_time ? event.starting_time.slice(0, 5) : '';
    const end = event.ending_time ? event.ending_time.slice(0, 5) : '';
    return `
      <div>
        <strong>School:</strong> ${school}<br>
        <strong>Start:</strong> ${start}<br>
        <strong>End:</strong> ${end}
      </div>`;
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
      if (Date.now() - cacheData.timestamp < 10000) {
        return cacheData;
      } else {
        sessionStorage.removeItem(cacheKey);
      }
    }
    return null;
  },

showEventDetails(eventId, occurrenceDate) {
    $('#eventId').val(String(eventId));
        if (!eventId || !occurrenceDate) {
            this.showNotification('error', "No event or occurrence date selected");
            return;
        }

    // Stop any existing polling to avoid conflicts
    this.stopPolling();

    // Reset button states
    $('#editEventBtn').removeClass('disabled').prop('disabled', false);
    $('#joinMeetingBtn').removeClass('disabled').prop('disabled', false);
    $('#deleteevent').removeClass('disabled').prop('disabled', false);
    $('#participantCount').text('0');
    $('#joinMeetingBtn').text('<?php echo get_phrase('Start Meeting'); ?>');

    const today = new Date();
    const startDate = this.formatDate(new Date(today.setFullYear(today.getFullYear() - 1)));
    const endDate = this.formatDate(new Date(today.setFullYear(today.getFullYear() + 2)));

    $.ajax({
            url: '<?php echo site_url('admin/get_events'); ?>',
            type: 'GET',
            data: { id: eventId, start_date: startDate, end_date: endDate, [csrfName]: csrfHash },
            success: (response) => {
                const data = JSON.parse(response);
                if (data.status === 'success' && data.data && data.data.length > 0) {
                    const event = data.data[0];
                    event.occurrence_date = occurrenceDate;
                    event.occurrences = event.occurrences || {};

                    const occurrenceData = event.occurrences[occurrenceDate] || {};
                    const isVisio = event.visio == 1;
                    const isExpired = event.recurrence_type !== 'does_not_repeat' && occurrenceData
                        ? occurrenceData.is_expired !== undefined ? occurrenceData.is_expired : event.is_expired
                        : event.is_expired;
                    // Populate view fields
                    $('#eventCreator').text(this.escapeHtml(event.created_by_name || 'Unknown'));
                    $('#eventTitle').text(this.escapeHtml(event.title || ''));
                    $('#eventDescriptionView').text(event.description || 'No description');
                    $('#eventSchool').text(event.school_name || event.title || '');
                    $('#eventClass').text(event.class_name || '');
                    $('#eventStart').text(`${event.starting_time ? event.starting_time.slice(0, 5) : ''}`);
                    $('#eventEnd').text(`${event.ending_time ? event.ending_time.slice(0, 5) : ''}`);
                    $('#eventRecurrenceSection').toggle(event.recurrence_type !== 'does_not_repeat');

                    $.ajax({
                        url: '<?php echo site_url('admin/get_school_data'); ?>',
                        type: 'POST',
                        data: {
                            school_id: event.school_id,
                            [csrfName]: csrfHash
                        },
                        success: (response) => {
                            try {
                                const data = JSON.parse(response);
                                if (data.status === 'success') {
                                    const userMap = {};
                                    if (data.users && Array.isArray(data.users)) {
                                        data.users.forEach(user => {
                                            userMap[user.id] = user.name;
                                        });
                                    }

                                    const classMap = {};
                                    if (data.classes && Array.isArray(data.classes)) {
                                        data.classes.forEach(cls => {
                                            classMap[cls.id] = cls.name;
                                        });
                                    }

                                    const eventParticipants = event.participants && Array.isArray(event.participants) ? event.participants : [];
                                    const participantsContainer = $('#eventParticipants');
                                    participantsContainer.empty();
                                    eventParticipants.forEach(p => {
                                        let name = 'Unknown';
                                        if (p.type === 'class' && classMap[p.id]) {
                                            name = classMap[p.id];
                                        } else if (p.type === 'individual' && userMap[p.id]) {
                                            name = userMap[p.id];
                                        }
                                        const badge = $(`
                                            <span class="badge" data-type="${p.type}" data-id="${p.id}">
                                                ${this.escapeHtml(name)}
                                            </span>
                                        `);
                                        participantsContainer.append(badge);
                                    });

                                    csrfHash = data.csrf.csrfHash;
                                }
                            } catch (e) {
                                this.showNotification('error', "Error parsing school data");
                            }
                        },
                    });

                    // Populate edit form fields
                    $('#eventId').val(event.id);
                    $('#eventEditModal').find('#currentOccurrenceDate').remove();
                    $('#eventEditModal').append('<input type="hidden" id="currentOccurrenceDate" value="' + occurrenceDate + '">');
                    $('#eventTitleInput').val(this.escapeHtml(event.title || ''));
                    $('#eventDescription').val(event.description || '');
                    $('#school_id').val(event.school_id || '');
                    $('#classe_id').val(event.class_id || '');
                    $('#eventDate').val(event.starting_date || '');
                    $('#eventEndDate').val(event.ending_date || '');
                    $('#recurrenceType').val(event.recurrence_type || 'does_not_repeat');
                    $('#recurrenceEndDate').val(event.recurrence_end_date || '');
                    $('#customRecurrence').val(event.custom_recurrence || '');
                    $('#visio').prop('checked', event.visio == 1);

                    // Set minimum date for date inputs
                    const todayStr = this.formatDate(new Date());
                    $('#eventDate').attr('min', todayStr);
                    $('#eventEndDate').attr('min', todayStr);

                    // Format time values to HH:mm
                    const startTimeFormatted = event.starting_time ? event.starting_time.slice(0, 5) : '';
                    const endTimeFormatted = event.ending_time ? event.ending_time.slice(0, 5) : '';

                    // Store values in hidden inputs for persistence
                    $('#eventForm').find('#tempStartTime, #tempEndTime').remove();
                    $('#eventForm').append('<input type="hidden" id="tempStartTime" value="' + startTimeFormatted + '">');
                    $('#eventForm').append('<input type="hidden" id="tempEndTime" value="' + endTimeFormatted + '">');

                    // Generate time options and set start_time and end_time
                    const selectedDate = $('#eventDate').val() || todayStr;
                    this.generateTimeOptions($('#eventStartTime'), selectedDate, true);
                    this.generateTimeOptions($('#eventEndTime'), selectedDate, true);

                    // Wait for options to be fully rendered
                    const waitForOptions = () => {
                        const startOptionsCount = $('#eventStartTime option').length;
                        const endOptionsCount = $('#eventEndTime option').length;
                        if (startOptionsCount > 1 && endOptionsCount > 1) {
                            $('#eventStartTime').val(startTimeFormatted);
                            $('#eventEndTime').val(endTimeFormatted);

                            // Verify if the values are correctly set
                            const startTimeSet = $('#eventStartTime').val();
                            const endTimeSet = $('#eventEndTime').val();  
                        } else {
                            setTimeout(waitForOptions, 50);
                        }
                    };
                    setTimeout(waitForOptions, 0);

                    // Load classes for the school
                    $.ajax({
                        url: '<?php echo site_url('admin/get_school_data'); ?>',
                        type: 'POST',
                        data: { school_id: event.school_id, [csrfName]: csrfHash },
                        success: (response) => {
                            try {
                                const data = JSON.parse(response);
                                if (data.status === 'success') {
                                    const classSelect = $('#classe_id');
                                    classSelect.empty();
                                    classSelect.append('<option value=""><?php echo get_phrase("select_a_class"); ?></option>');
                                    if (data.classes && Array.isArray(data.classes)) {
                                        data.classes.forEach(cls => {
                                            classSelect.append(`<option value="${cls.id}">${this.escapeHtml(cls.name)}</option>`);
                                        });
                                    }
                                    classSelect.val(event.class_id || '');
                                    csrfHash = data.csrf.csrfHash;
                                }
                            } catch (e) {
                                this.showNotification('error', "Error parsing school data");
                            }
                        },
                        error: () => {
                            this.showNotification('error', "Failed to load classes");
                        }
                    });
                    const participants = event.participants && Array.isArray(event.participants) ? event.participants : [];
                    this.refreshUsersDropdown(event.school_id, participants);
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

                    $('#eventParticipantsSection').toggle(isVisio);
                    $('#joinMeetingBtn').toggle(isVisio);

                    if (isExpired) {
                        $('#joinMeetingBtn').addClass('disabled').prop('disabled', true);
                        $('#editEventBtn').addClass('disabled').prop('disabled', true);
                        $('#deleteevent').removeClass('disabled').prop('disabled', false);
                        $('#participantCount').text('0');
                        $('#joinMeetingBtn').text('<?php echo get_phrase('Start Meeting'); ?>');
                        this.stopPolling();
                    } else if (isVisio) {
    const meetingId = occurrenceData.meeting_id;
                        if (meetingId) {
                            $.ajax({
                                url: '<?php echo site_url('bigbluebutton/meeting_states'); ?>',
                                type: 'POST',
                                data: { meetingIDs: [meetingId], [csrfName]: csrfHash },
                                dataType: 'json',
                                success: (response) => {
                                    const status = response.status ? String(response.status).trim().toLowerCase() : '';
                                    if (status === 'success' && response.data && response.data.length > 0) {
                                        const state = response.data[0];
                                        if (state.status === 'success' && String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                            this.cacheMeetingState(meetingId, state.participant_count, state.is_running);
                                            this.updateParticipantUI(eventId, state.participant_count, state.is_running, occurrenceDate);
                                            $('#deleteevent').prop('disabled', state.is_running).toggleClass('disabled', state.is_running);
                                            $('#joinMeetingBtn').text(state.is_running ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Start Meeting'); ?>').prop('disabled', false).removeClass('disabled');
                                            if (state.is_running) {
                                                this.hasActiveMeetings = true;
                                                this.pollActiveMeetings();
                                            }
                                            this.startPolling(eventId, meetingId, occurrenceDate);
                                            csrfHash = response.csrf?.csrfHash || csrfHash;
                                        } else {
                                            $('#participantCount').text('0');
                                            $('#joinMeetingBtn').text('<?php echo get_phrase('Start Meeting'); ?>').prop('disabled', false).removeClass('disabled');
                                            $('#deleteevent').prop('disabled', false).removeClass('disabled');
                                        }
                                    } else {
                                        $('#participantCount').text('0');
                                        $('#joinMeetingBtn').text('<?php echo get_phrase('Start Meeting'); ?>').prop('disabled', false).removeClass('disabled');
                                        $('#deleteevent').prop('disabled', false).removeClass('disabled');
                                    }
                                },
                                error: (xhr, status, error) => {
                                    const cachedState = this.getCachedMeetingState(meetingId);
                                    if (cachedState && String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                        this.updateParticipantUI(eventId, cachedState.participantCount, cachedState.isRunning, occurrenceDate);
                                        $('#deleteevent').prop('disabled', cachedState.isRunning).toggleClass('disabled', cachedState.isRunning);
                                        $('#joinMeetingBtn').text(cachedState.isRunning ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Start Meeting'); ?>').prop('disabled', false).removeClass('disabled');
                                        if (cachedState.isRunning) {
                                            this.hasActiveMeetings = true;
                                            this.pollActiveMeetings();
                                        }
                                        this.startPolling(eventId, meetingId, occurrenceDate);
                                    } else {
                                        $('#participantCount').text('0');
                                        $('#joinMeetingBtn').text('<?php echo get_phrase('Start Meeting'); ?>').prop('disabled', false).removeClass('disabled');
                                        $('#deleteevent').prop('disabled', false).removeClass('disabled');
                                    }
                                }
                            });
                        } else {
                            $('#participantCount').text('0');
                            $('#joinMeetingBtn').text('<?php echo get_phrase('Start Meeting'); ?>').prop('disabled', false).removeClass('disabled');
                            $('#deleteevent').prop('disabled', false).removeClass('disabled');
                            this.stopPolling();
                        }
                    }

          $('#recurrenceTypePopup').val(event.recurrence_type || 'does_not_repeat');
                    $('#recurrenceEndDatePopup').val(event.recurrence_end_date || '');
                    $('#customRecurrencePopup').val(event.custom_recurrence || '');
                    $('#recurrenceStartDateSection').toggle(event.recurrence_type === 'monthly' || event.recurrence_type === 'yearly');
                    if (event.recurrence_type === 'weekly' && event.custom_recurrence) {
                        try {
                            this.selectedDays = JSON.parse(event.custom_recurrence) || [];
                            $('.day-btn').removeClass('active');
                            this.selectedDays.forEach(day => {
                                $(`.day-btn[data-day="${day}"]`).addClass('active');
                            });
                            $('#daySelection').show();
                        } catch (e) {
                            console.error('Error parsing custom_recurrence:', e);
                        }
                    } else if (event.recurrence_type === 'daily') {
                        this.selectedDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                        $('.day-btn').addClass('active');
                        $('#daySelection').show();
                    } else {
                        this.selectedDays = [];
                        $('.day-btn').removeClass('active');
                        $('#daySelection').hide();
                    }

                    $('#eventEditModal').off('hidden.bs.modal').on('hidden.bs.modal', () => {
                        $('#editEventBtn').removeClass('disabled').prop('disabled', false);
                        $('#joinMeetingBtn').removeClass('disabled').prop('disabled', false);
                        $('#deleteevent').removeClass('disabled').prop('disabled', false);
                        $('#participantCount').text('0');
                        $('#joinMeetingBtn').text('<?php echo get_phrase('Start Meeting'); ?>');
                        $('#eventEditModalLabel').text('<?php echo get_phrase("event_details"); ?>');
                        $('#tempStartTime, #tempEndTime').remove();
                        $('#currentOccurrenceDate').remove();
                        this.stopPolling();
                        if (this.hasActiveMeetings && document.visibilityState === 'visible') {
                            this.pollActiveMeetings();
                        }
                    });

                    $('#eventDetailsView').show();
                    $('#eventForm').hide();
                    $('#eventEditModal').modal('show');
                    csrfHash = data.csrf.csrfHash;
                } else {
                    this.showNotification('error', data.message || 'Failed to load event');
                }
            },
            error: () => {
                this.showNotification('error', "Failed to load event");
                console.error('showEventDetails - AJAX error fetching get_events');
                this.stopPolling();
            }
        });
    },

startPolling(eventId, meetingId, occurrenceDate) {
    if (document.visibilityState !== 'visible') return;

    this.stopPolling();
    this.currentEventId = eventId;
    this.currentMeetingId = meetingId;
    this.currentOccurrenceDate = occurrenceDate;


    let lastPollTime = 0;
    const poll = () => {
        if (document.visibilityState !== 'visible') {
            this.stopPolling();
            return;
        }
        const now = Date.now();
        if (now - lastPollTime < 1500) return; // Prevent overlapping polls
        lastPollTime = now;

        const cachedState = this.getCachedMeetingState(meetingId);
        if (cachedState && Date.now() - cachedState.timestamp < 10000) {
            const modalEventId = $('#eventId').length > 0 ? String($('#eventId').val()) : null;
            const modalOccurrenceDate = $('#currentOccurrenceDate').length > 0 ? $('#currentOccurrenceDate').val() : null;
            if (modalEventId && modalOccurrenceDate && modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                this.updateParticipantUI(eventId, cachedState.participantCount, cachedState.isRunning, occurrenceDate);
                 $('#joinMeetingBtn').text(cachedState.isRunning ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', !cachedState.isRunning).removeClass('disabled');
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
                            $('#joinMeetingBtn').text(state.is_running ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', !state.is_running).removeClass('disabled');
                            csrfHash = response.csrf?.csrfHash || csrfHash;
                        }
                    } else {
                        if (modalEventId && modalOccurrenceDate && modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                            $('#participantCount').text('0');
                            $('#joinMeetingBtn').text('<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', true).removeClass('disabled');
                        }
                        this.stopPolling();
                    }
                } else {
                    if (modalEventId && modalOccurrenceDate && modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                        $('#participantCount').text('0');
                        $('#joinMeetingBtn').text('<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', true).removeClass('disabled');
                    }
                    this.stopPolling();
                }
            },
            error: () => {
                const modalEventId = $('#eventId').length > 0 ? String($('#eventId').val()) : null;
                const modalOccurrenceDate = $('#currentOccurrenceDate').length > 0 ? $('#currentOccurrenceDate').val() : null;
                if (modalEventId && modalOccurrenceDate && modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                    $('#participantCount').text('0');
                    $('#joinMeetingBtn').text('<?php echo get_phrase('Meeting Not Started'); ?>').prop('disabled', true).removeClass('disabled');
                }
                this.stopPolling();
            }
        });
    };

    poll(); // Immediate check
    this.pollingInterval = setInterval(poll, 1500);
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
  validateForm(formId) {
    const form = $(`#${formId}`);
    const requiredFields = form.find('[required]');
    let isValid = true;
    requiredFields.each(function() {
      const field = $(this);
      if (!field.val().trim()) { field.addClass('is-invalid'); isValid = false; }
      else field.removeClass('is-invalid');
    });

    const startDate = form.find('[name="start"]').val();
    const endDate = form.find('[name="end_date"]').val() || startDate;
    const startTime = form.find('[name="start_time"]').val();
    const endTime = form.find('[name="end_time"]').val();

    if (startDate && endDate && startTime && endTime && startDate === endDate) {
      const st = parseInt(startTime.split(':')[0]) * 60 + parseInt(startTime.split(':')[1]);
      const et = parseInt(endTime.split(':')[0]) * 60 + parseInt(endTime.split(':')[1]);
      if (et <= st) {
        this.showNotification('error', "End time must be after start time");
        form.find('[name="end_time"]').addClass('is-invalid');
        isValid = false;
      } else form.find('[name="end_time"]').removeClass('is-invalid');
    }
    return isValid;
  },

  escapeHtml(str) {
    if (typeof str !== 'string') return '';
    return str.replace(/[&<>"']/g, (m) => ({
      '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'
    }[m]));
  },

  updateRecurrenceType() {
    const allDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    const n = this.selectedDays.length;
    if (n === allDays.length) {
      $('#recurrenceTypePopup').val('daily');
      $('#daySelection').show();
      $('.day-btn').addClass('active');
    } else if (n > 0) {
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
      $('#participantsSearchInput').val('');
      $('#participantsBadges').empty();
      $('#participantsDropdownMenu').html('<div style="padding: 10px; text-align: center; color: #6c757d;"><?php echo get_phrase("Write something to search..."); ?></div>');
      $('#participantsDropdownMenu').removeClass('show').parent().removeClass('open');
      $('#participantsInput').val('[]');
      $('#participantsDropdownMenu .participant-checkbox').prop('checked', false);
      $('#createeventEndDate').on('blur', () => {
        const startDate = document.getElementById('createeventDate').value;
        const endDate = $('#createeventEndDate').val();
        if (startDate && endDate && endDate < startDate) {
          CalendarApp.showNotification('error', "<?php echo get_phrase('end_date_must_be_on_or_after_start_date'); ?>");
          $('#createeventEndDate').val('');
        }
      });

      const today = this.formatDate(new Date());
      $('#createeventDate').attr('min', today);
      $('#createeventEndDate').attr('min', today);
      $('#createeventStartTime').prop('disabled', true);
      $('#createeventEndTime').prop('disabled', true);
       const updateTimeFields = () => {
      const inputElement = document.getElementById('createeventDate');
      const selectedDate = inputElement ? inputElement.value : '';
    if (selectedDate) {
      $('#createeventStartTime').prop('disabled', false);
      $('#createeventEndTime').prop('disabled', false);
      CalendarApp.generateTimeOptions($('#createeventStartTime'), selectedDate);
      CalendarApp.generateTimeOptions($('#createeventEndTime'), selectedDate);
    } else {
      $('#createeventStartTime').prop('disabled', true).val('');
      $('#createeventEndTime').prop('disabled', true).val('');
    }
  };
      updateTimeFields();
      setTimeout(() => {
    updateTimeFields();
  }, 2000);
      $('#createeventDate').off('change.timeOptions input.timeOptions click.timeOptions').on('change.timeOptions input.timeOptions click.timeOptions', () => {
    const dateValue = document.getElementById('createeventDate').value;
    updateTimeFields();
    $('#recurrenceStartDatePopup').val(dateValue);
  });
      const selectedDate = $('#createeventDate').val() || today;
      this.generateTimeOptions($('#createeventStartTime'), selectedDate);
      this.generateTimeOptions($('#createeventEndTime'), selectedDate);

      $('#createeventDate').off('change.timeOptions').on('change.timeOptions', () => {
        const sd = $('#createeventDate').val();
        this.generateTimeOptions($('#createeventStartTime'), sd);
        this.generateTimeOptions($('#createeventEndTime'), sd);
        $('#recurrenceStartDatePopup').val(sd);
      });

      this.resetRecurrenceModal();

      $.ajax({
        url: '<?php echo site_url('admin/get_user_school'); ?>',
        type: 'GET',
        data: { [csrfName]: csrfHash },
        success: (response) => {
            const data = JSON.parse(response);
            if (data.status === 'success') {
                const schoolSelect = $('#createSchoolId');
                schoolSelect.empty();
                schoolSelect.append(`<option value="${data.data.id}">${this.escapeHtml(data.data.name)}</option>`);
                schoolSelect.prop('disabled', true);
                csrfHash = data.csrf.csrfHash;

              // Load school data and populate participants select
              CalendarApp.refreshUsersDropdown(data.data.id, []);
        } else {
            this.showNotification('error', data.message);
        }
    },
    error: () => { this.showNotification('error', 'Failed to load school'); }
});
    });


    $('#createEventForm').on('submit', (e) => {
      e.preventDefault();
      if (!this.validateForm('createEventForm')) {
        this.showNotification('error', "Please fill all required fields");
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
        participants: $('#participantsInput').val(),
        [csrfName]: csrfHash
      };
      $.ajax({
        url: '<?php echo site_url('admin/create_event'); ?>',
        type: 'POST',
        data: formData,
        success: (response) => {
            const data = JSON.parse(response);
            if (data.status === 'success') {
              $('#createEventModal').modal('hide');
              $('#createEventForm')[0].reset();
              this.resetRecurrenceModal();
              CalendarApp.resetPicker('create');
              this.showNotification('success', data.message);
              this.clearEventCache();
              this.calendar.refetchEvents();
              this.calendar.render();
            } else {
              this.showNotification('error', data.message);
            }
            csrfHash = data.csrf.csrfHash;
        },
        error: () => { this.showNotification('error', "Failed to create event"); }
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
    participants: $('#editParticipantsInput').val(),
    [csrfName]: csrfHash
  };
  $.ajax({
    url: '<?php echo site_url('admin/update_event'); ?>',
    type: 'POST',
    data: formData,
    success: (response) => {
        const data = JSON.parse(response);
        if (data.status === 'success') {
          $('#eventEditModal').modal('hide');
          this.resetRecurrenceModal();
          this.showNotification('success', data.message);
          this.clearEventCache();
          this.calendar.refetchEvents();
          this.calendar.render();
        } else {
          this.showNotification('error', data.message);
        }
        csrfHash = data.csrf.csrfHash;
    },
    error: () => { this.showNotification('error', "Failed to update event"); }
  });
});

    $('#deleteevent').on('click', () => {
  const eventId = $('#eventId').val();
  if (!eventId) {
            this.showNotification('error', "No event selected");
    return;
  }

  Swal.fire({
    title: '<?php echo addslashes(get_phrase("are_you_sure")); ?>',
    text: '<?php echo addslashes(get_phrase("you_will_not_be_able_to_revert_this")); ?>',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#6366f1',
    cancelButtonColor: '#6c757d',
    confirmButtonText: '<?php echo addslashes(get_phrase("yes_delete_it")); ?>',
    cancelButtonText: '<?php echo addslashes(get_phrase("cancel")); ?>',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        url: '<?php echo site_url('admin/delete_event'); ?>',
        type: 'POST',
        data: { id: eventId, [csrfName]: csrfHash },
        success: (response) => {
          try {
            const data = JSON.parse(response);
            if (data.status === 'success') {
              $('#eventEditModal').modal('hide');
              this.showNotification('success', data.message);
              this.clearEventCache();
              this.calendar.refetchEvents();
              this.calendar.render();
              csrfHash = data.csrf.csrfHash;
            } else {
              this.showNotification('error', data.message);
            }
          } catch (e) {
            this.showNotification('error', "Invalid server response");
          }
        },
        error: (xhr) => {
          console.error('Delete event AJAX error:', xhr.status, xhr.statusText);
          this.showNotification('error', "Failed to delete event");
        }
      });
    }
  });
});

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

      const targetForm = $('#createEventModal').hasClass('show') ? $('#createEventForm') : $('#eventForm');

      targetForm.find('[name="recurrence_type"]').val(recurrenceType);
      targetForm.find('[name="recurrence_end_date"]').val(recurrenceEndDate);
      targetForm.find('[name="custom_recurrence"]').val(customRecurrence);
      $('#recurrenceModal').modal('hide');
    });
    $('#joinMeetingBtn').on('click', () => { this.startMeeting(); });
  },

  previousPeriod() { this.calendar.prev(); },
  nextPeriod() { this.calendar.next(); },
  goToToday() { this.calendar.today(); },

updateParticipantUI(eventId, participantCount, isRunning, occurrenceDate) {
    this.closeAllPopovers();

    const modalEventId = String($('#eventId').val());
    const modalOccurrenceDate = $('#currentOccurrenceDate').val();
    const uniqueEventId = occurrenceDate ? `${eventId}_${occurrenceDate}` : eventId;

    // Mettre à jour l'UI du modal si ouvert
    if (modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
        $('#participantCount').text(participantCount || 0);
        $('#joinMeetingBtn').text(isRunning ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Start Meeting'); ?>').prop('disabled', !isRunning);
        $('#deleteevent').prop('disabled', isRunning).toggleClass('disabled', isRunning); // Griser/dégriser le bouton delete
    }

    // Mettre à jour l'événement dans le calendrier
    const event = this.calendar.getEventById(uniqueEventId);
    if (event) {
        event.setExtendedProp('isRunning', isRunning);
        event.setExtendedProp('participant_count', participantCount);
        // Forcer le re-rendu pour appliquer/supprimer la classe active-meeting
        const eventData = {
            id: event.id,
            title: event.title,
            start: event.start,
            end: event.end,
            extendedProps: { ...event.extendedProps, isRunning, participant_count: participantCount }
        };
        event.remove();
        this.calendar.addEvent(eventData);

        // Mettre à jour l'ensemble des réunions actives
        if (isRunning) {
            this.activeMeetings.add(JSON.stringify({ meetingId: event.extendedProps.meeting_id, eventId, occurrenceDate }));
        } else {
            this.activeMeetings.delete(JSON.stringify({ meetingId: event.extendedProps.meeting_id, eventId, occurrenceDate }));
            sessionStorage.removeItem(`meeting_state_${event.extendedProps.meeting_id}`);
        }
    }

    // Update other occurrences to ensure only the correct occurrence is marked active
    const today = new Date();
    const startDate = this.formatDate(new Date(today.setFullYear(today.getFullYear() - 1)));
    const endDate = this.formatDate(new Date(today.setFullYear(today.getFullYear() + 2)));
    $.ajax({
        url: '<?php echo site_url('admin/get_events'); ?>',
        type: 'GET',
        data: { id: eventId, start_date: startDate, end_date: endDate, [csrfName]: csrfHash },
        success: (response) => {
            try {
                const data = JSON.parse(response);
                if (data.status === 'success' && data.data && data.data.length > 0) {
                  this.closeAllPopovers();
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
                                        // Force re-render to apply/remove active-meeting class
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
                                        // Force re-render to remove active-meeting class
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
            console.error('updateParticipantUI - AJAX error:', xhr.status, xhr.statusText);
        }
    });
},

startMeeting() {
    const eventId = String($('#eventId').val());
    const occurrenceDate = $('#currentOccurrenceDate').val();
    
    if (!eventId || !occurrenceDate) {
        this.showNotification('error', "No event or occurrence date selected");
        return;
    }

    // Clear previous polling and state
    this.stopPolling();
    this.currentMeetingId = null;
    this.currentEventId = null;
    this.currentOccurrenceDate = null;

    const today = new Date();
    const startDate = this.formatDate(new Date(today.setFullYear(today.getFullYear() - 1)));
    const endDate = this.formatDate(new Date(today.setFullYear(today.getFullYear() + 2)));

    $.ajax({
        url: '<?php echo site_url('admin/get_events'); ?>',
        type: 'GET',
        data: { id: eventId, start_date: startDate, end_date: endDate, [csrfName]: csrfHash },
        async: true,
        success: (response) => {
            try {
                const data = JSON.parse(response);
                if (data.status === 'success' && data.data && data.data.length > 0) {
                    const event = data.data[0];
                    const occurrenceData = event.occurrences && event.occurrences[occurrenceDate] ? event.occurrences[occurrenceDate] : {};

                    let isExpired;
                    if (event.recurrence_type !== 'does_not_repeat' && occurrenceDate !== event.starting_date) {
                        isExpired = occurrenceData.is_expired !== undefined ? occurrenceData.is_expired : event.is_expired;
                    } else {
                        isExpired = event.is_expired;
                    }

                    if (isExpired === undefined || isExpired === null) {
                        const effectiveEndDate = event.recurrence_type !== 'does_not_repeat' ? occurrenceDate : (event.ending_date || event.starting_date);
                        const endingTime = event.ending_time ? event.ending_time.slice(0, 5) : '23:59';
                        let endDateTime;
                        try {
                            endDateTime = new Date(`${effectiveEndDate}T${endingTime}:00Z`);
                            if (isNaN(endDateTime.getTime())) {
                                throw new Error('Invalid endDateTime');
                            }
                            isExpired = new Date() - endDateTime > 24 * 60 * 60 * 1000;
                        } catch (e) {
                            this.showNotification('error', "Invalid event date or time format");
                            $('#joinMeetingBtn').show();
                            return;
                        }
                    }

                    if (isExpired) {
                        this.showNotification('error', "Event occurrence is expired");
                        $('#joinMeetingBtn').show();
                        return;
                    }
                    if (event.visio != 1) {
                        this.showNotification('error', "This event does not support video conferencing");
                        $('#joinMeetingBtn').show();
                        return;
                    }

                    const buttonText = $('#joinMeetingBtn').text();

                    if (buttonText === '<?php echo get_phrase('Start Meeting'); ?>') {
                        $.ajax({
                            url: '<?php echo site_url('admin/start_meeting'); ?>',
                            type: 'POST',
                            data: { event_id: eventId, occurrence_date: occurrenceDate, [csrfName]: csrfHash },
                            success: (response) => {
                                try {
                                    const data = JSON.parse(response);
                                    csrfHash = data.csrf.csrfHash;

                                    if (data.status === 'success' && data.meeting_id && data.appointment_id) {
                                        // Cache the new meeting state
                                        this.cacheMeetingState(data.meeting_id, data.participant_count, data.is_running);

                                        // Update UI if the modal is still open for this event and occurrence
                                        if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                            this.updateParticipantUI(eventId, data.participant_count, data.is_running, occurrenceDate);
                                            $('#joinMeetingBtn').hide();
                                        }

                                        // Update event in calendar
                                        const uniqueEventId = occurrenceDate ? `${eventId}_${occurrenceDate}` : eventId;
                                        const calendarEvent = this.calendar.getEventById(uniqueEventId);
                                        if (calendarEvent) {
                                            calendarEvent.setExtendedProp('isRunning', data.is_running);
                                            calendarEvent.setExtendedProp('participant_count', data.participant_count);
                                            calendarEvent.setExtendedProp('meeting_id', data.meeting_id);
                                            this.calendar.render();
                                        }

                                        // Start polling for the new meeting
                                        this.startPolling(eventId, data.meeting_id, occurrenceDate);

                                        // Resume active meetings polling if needed
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
                                            this.showNotification('warning', "Unable to open meeting. Please allow pop-ups for this site or click <a href=\"" + joinUrl + "\" target=\"_blank\">here</a> to join.", 5000);
                                            $('#joinMeetingBtn').show();
                                        } else {
                                            this.showNotification('success', "Starting meeting...");
                                            const checkWindowClosed = setInterval(() => {
                                                if (newWindow.closed) {
                                                    clearInterval(checkWindowClosed);
                                                    if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                                        $('#joinMeetingBtn').show();
                                                    }
                                                    this.stopPolling();
                                                    if (this.hasActiveMeetings && document.visibilityState === 'visible') {
                                                        this.pollActiveMeetings();
                                                    }
                                                }
                                            }, 1000);
                                        }
                                    } else {
                                        this.showNotification('error', data.message || 'Failed to start meeting');
                                        $('#joinMeetingBtn').show();
                                    }
                                } catch (e) {
                                    this.showNotification('error', "Invalid server response");
                                    $('#joinMeetingBtn').show();
                                }
                            },
                            error: (xhr) => {
                                $('#joinMeetingBtn').show();
                            }
                        });
                    } else if (buttonText === '<?php echo get_phrase('Join Meeting'); ?>') {
                        if (!occurrenceData.meeting_id) {
                            $('#joinMeetingBtn').show();
                            return;
                        }

                        // Fetch the latest meeting state before joining
                        $.ajax({
                            url: '<?php echo site_url('bigbluebutton/meeting_states'); ?>',
                            type: 'POST',
                            data: { meetingIDs: [occurrenceData.meeting_id], [csrfName]: csrfHash },
                            dataType: 'json',
                            success: (response) => {
                                const status = response.status ? String(response.status).trim().toLowerCase() : '';
                                if (status === 'success' && response.data && response.data.length > 0) {
                                    const state = response.data[0];
                                    if (state.status === 'success') {
                                        this.cacheMeetingState(occurrenceData.meeting_id, state.participant_count, state.is_running);
                                        if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                            this.updateParticipantUI(eventId, state.participant_count, state.is_running, occurrenceDate);
                                            $('#joinMeetingBtn').hide();
                                        }
                                        const uniqueEventId = occurrenceDate ? `${eventId}_${occurrenceDate}` : eventId;
                                        const calendarEvent = this.calendar.getEventById(uniqueEventId);
                                        if (calendarEvent) {
                                            calendarEvent.setExtendedProp('isRunning', state.is_running);
                                            calendarEvent.setExtendedProp('participant_count', state.participant_count);
                                            this.calendar.render();
                                        }
                                        if (state.is_running && !this.activeMeetingsPollingInterval) {
                                            this.hasActiveMeetings = true;
                                            this.pollActiveMeetings();
                                        }
                                        this.startPolling(eventId, occurrenceData.meeting_id, occurrenceDate);
                                    } else {
                                        this.showNotification('error', state.message || "Meeting is not active");
                                        $('#joinMeetingBtn').show();
                                        return;
                                    }
                                } else {
                                    this.showNotification('error', "Failed to verify meeting state");
                                    $('#joinMeetingBtn').show();
                                    return;
                                }
                            },
                            error: (xhr, status, error) => {
                                this.showNotification('error', 'Failed to verify meeting state');
                                $('#joinMeetingBtn').show();
                                return;
                            }
                        });

                        const joinUrl = '<?php echo site_url('bigbluebutton/join_meeting'); ?>/' + encodeURIComponent(occurrenceData.meeting_id);
                        const newWindow = window.open(joinUrl, '_blank');
                        if (!newWindow || newWindow.closed || typeof newWindow.closed === 'undefined') {
                            this.showNotification('warning', "Unable to open meeting. Please allow pop-ups for this site or click <a href=\"" + joinUrl + "\" target=\"_blank\">here</a> to join.", 5000);
                            $('#joinMeetingBtn').show();
                        } else {
                            this.showNotification('success', "Joining meeting...");
                            this.startPolling(eventId, occurrenceData.meeting_id, occurrenceDate);
                             // Monitor window close to show the button again
                            const checkWindowClosed = setInterval(() => {
                                if (newWindow.closed) {
                                    clearInterval(checkWindowClosed);
                                    if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                        $('#joinMeetingBtn').show();
                                    }
                                    this.stopPolling();
                                    if (this.hasActiveMeetings && document.visibilityState === 'visible') {
                                        this.pollActiveMeetings();
                                    }
                                }
                            }, 1000);
                        }
                    }
                    csrfHash = data.csrf.csrfHash;
                } else {
                    this.showNotification('error', data.message || 'Failed to load event');
                    $('#joinMeetingBtn').show(); 
                }
            } catch (e) {
                this.showNotification('error', "Error processing event data");
                $('#joinMeetingBtn').show();
            }
        },
        error: (xhr) => {
            this.showNotification('error', "Failed to load event");
            $('#joinMeetingBtn').show(); 
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

        const today = new Date();
        const currentDate = this.formatDate(today);

        $.ajax({
            url: '<?php echo site_url('admin/get_events'); ?>',
            type: 'GET',
            data: { start_date: currentDate, end_date: currentDate, visio: 1, [csrfName]: csrfHash },
            success: (response) => {
                try {
                    const data = JSON.parse(response);
                    if (data.status === 'success' && data.data) {
                        this.closeAllPopovers();
                        const meetingIds = new Set();
                        const currentActiveMeetings = new Set();
                        data.data.forEach(event => {
                            const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
                            const isExpired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);
                            if (!isExpired && event.visio == 1 && event.starting_date === currentDate) {
                                if (event.occurrences && Object.keys(event.occurrences).length > 0) {
                                    Object.keys(event.occurrences).forEach(occurrenceDate => {
                                    const meetingId = event.occurrences[occurrenceDate]?.meeting_id;
                                    if (meetingId && occurrenceDate === currentDate) {
                                        const occurrenceEndDateTime = new Date(`${occurrenceDate}T${event.ending_time}`);
                                        const isOccurrenceExpired = occurrenceEndDateTime && (new Date() - occurrenceEndDateTime > 24 * 60 * 60 * 1000);
                                        if (!isOccurrenceExpired) {
                                            meetingIds.add(JSON.stringify({ meetingId, eventId: event.id, occurrenceDate }));
                                        }
                                    }
                                });
                            } else if (event.meeting_id && event.starting_date === currentDate) {
                                meetingIds.add(JSON.stringify({ meetingId: event.meeting_id, eventId: event.id, occurrenceDate: event.starting_date }));
                            }
                            }
                        });

                        const meetingIdsArray = Array.from(meetingIds).map(item => JSON.parse(item));

                        if (meetingIdsArray.length === 0) {
                            this.closeAllPopovers();
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
                                if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                    $('#deleteevent').prop('disabled', false).removeClass('disabled');
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
                            if (cachedState && cachedState.isRunning) {
                                cachedResults.push({ meetingId, eventId, occurrenceDate, ...cachedState });
                            } else {
                                meetingIdsToFetch.push(meetingId);
                            }
                        });

                        this.activeMeetings.forEach(item => {
                            const parsedItem = JSON.parse(item);
                            if (!meetingIdsArray.some(m => m.meetingId === parsedItem.meetingId)) {
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
                                if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                    $('#deleteevent').prop('disabled', false).removeClass('disabled');
                                }
                                this.activeMeetings.delete(item);
                            }
                        });

                        $.ajax({
                            url: '<?php echo site_url('bigbluebutton/meeting_states'); ?>',
                            type: 'POST',
                            data: { meetingIDs: meetingIdsToFetch, [csrfName]: csrfHash },
                            dataType: 'json',
                            success: (response) => {
                                if (response.status === 'success' && response.data) {
                                    this.closeAllPopovers();
                                    let activeMeetingsFound = false;
                                    response.data.forEach(state => {
                                        const { meeting_id, status, participant_count, is_running } = state;
                                        const matchingMeeting = meetingIdsArray.find(item => item.meetingId === meeting_id);
                                        if (!matchingMeeting) return;

                                        const { eventId, occurrenceDate } = matchingMeeting;
                                        const uniqueEventId = occurrenceDate ? `${eventId}_${occurrenceDate}` : eventId;
                                        if (status === 'success') {
                                            this.cacheMeetingState(meeting_id, participant_count, is_running);
                                            const event = this.calendar.getEventById(uniqueEventId);
                                            if (event) {
                                                event.setExtendedProp('isRunning', is_running);
                                                event.setExtendedProp('participant_count', participant_count);
                                                const eventData = {
                                                    id: event.id,
                                                    title: event.title,
                                                    start: event.start,
                                                    end: event.end,
                                                    extendedProps: { ...event.extendedProps, isRunning: is_running, participant_count: participant_count }
                                                };
                                                event.remove();
                                                this.calendar.addEvent(eventData);
                                                if (is_running) {
                                                    activeMeetingsFound = true;
                                                    this.activeMeetings.add(JSON.stringify({ meetingId: meeting_id, eventId, occurrenceDate }));
                                                    const modalEventId = String($('#eventId').val());
                                                    const modalOccurrenceDate = $('#currentOccurrenceDate').val();
                                                    if (modalEventId === String(eventId) && modalOccurrenceDate === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                                        $('#participantCount').text(participant_count || 0);
                                                        $('#joinMeetingBtn').text(is_running ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Start Meeting'); ?>').prop('disabled', !is_running);
                                                        $('#deleteevent').prop('disabled', is_running).toggleClass('disabled', is_running); // Griser si actif
                                                    }
                                                } else {
                                                    sessionStorage.removeItem(`meeting_state_${meeting_id}`);
                                                    this.activeMeetings.delete(JSON.stringify({ meetingId: meeting_id, eventId, occurrenceDate }));
                                                    if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                                        $('#deleteevent').prop('disabled', false).removeClass('disabled'); // Réactiver si non actif
                                                    }
                                                }
                                            }
                                        } else {
                                            const event = this.calendar.getEventById(uniqueEventId);
                                            if (event) {
                                                event.setExtendedProp('isRunning', false);
                                                event.setExtendedProp('participant_count', 0);
                                                sessionStorage.removeItem(`meeting_state_${meeting_id}`);
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
                                            this.activeMeetings.delete(JSON.stringify({ meetingId: meeting_id, eventId, occurrenceDate }));
                                            if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                                $('#deleteevent').prop('disabled', false).removeClass('disabled'); // Réactiver si non actif
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
                                                extendedProps: { ...event.extendedProps, isRunning: isRunning, participant_count: participantCount }
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
                                                    $('#joinMeetingBtn').text(isRunning ? '<?php echo get_phrase('Join Meeting'); ?>' : '<?php echo get_phrase('Start Meeting'); ?>').prop('disabled', !isRunning);
                                                    $('#deleteevent').prop('disabled', isRunning).toggleClass('disabled', isRunning); // Griser si actif
                                                }
                                            } else {
                                                sessionStorage.removeItem(`meeting_state_${meetingId}`);
                                                this.activeMeetings.delete(JSON.stringify({ meetingId, eventId, occurrenceDate }));
                                                if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                                    $('#deleteevent').prop('disabled', false).removeClass('disabled'); // Réactiver si non actif
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
                                    this.closeAllPopovers();
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
                                        if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                            $('#deleteevent').prop('disabled', false).removeClass('disabled');
                                        }
                                    });
                                    this.activeMeetings.clear();
                                    this.hasActiveMeetings = false;
                                    this.isPolling = false;
                                    this.stopActiveMeetingsPolling();
                                }
                            },
                            error: (xhr, status, error) => {
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
                                    if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                        $('#deleteevent').prop('disabled', false).removeClass('disabled');
                                    }
                                });
                                this.activeMeetings.clear();
                                this.hasActiveMeetings = false;
                                this.isPolling = false;
                                this.stopActiveMeetingsPolling();
                            }
                        });
                    } else {
                        this.closeAllPopovers();
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
                            if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                                $('#deleteevent').prop('disabled', false).removeClass('disabled');
                            }
                        });
                        this.activeMeetings.clear();
                        this.hasActiveMeetings = false;
                        this.isPolling = false;
                        this.stopActiveMeetingsPolling();
                    }
                } catch (e) {
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
                        if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                            $('#deleteevent').prop('disabled', false).removeClass('disabled');
                        }
                    });
                    this.activeMeetings.clear();
                    this.hasActiveMeetings = false;
                    this.isPolling = false;
                    this.stopActiveMeetingsPolling();
                }
            },
            error: () => {
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
                    if (String($('#eventId').val()) === String(eventId) && $('#currentOccurrenceDate').val() === occurrenceDate && $('#eventEditModal').hasClass('show')) {
                        $('#deleteevent').prop('disabled', false).removeClass('disabled');
                    }
                });
                this.activeMeetings.clear();
                this.hasActiveMeetings = false;
                this.isPolling = false;
                this.stopActiveMeetingsPolling();
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
  const currentDate = this.formatDate(today);

  $.ajax({
    url: '<?php echo site_url('admin/get_events'); ?>',
    type: 'GET',
    data: { start_date: currentDate, end_date: currentDate, visio: 1, [csrfName]: csrfHash },
    success: (response) => {
      try {
        const data = JSON.parse(response);
        if (data.status === 'success' && data.data) {
          let hasActive = false;
          const meetingIdsToCheck = [];
          data.data.forEach(event => {
            const endDateTime = new Date(`${event.ending_date || event.starting_date}T${event.ending_time}`);
            const isExpired = endDateTime && (new Date() - endDateTime > 24 * 60 * 60 * 1000);
            if (!isExpired && event.visio == 1 && event.starting_date === currentDate && event.occurrences) {
                      Object.keys(event.occurrences).forEach(occurrenceDate => {
            const meetingId = event.occurrences[occurrenceDate]?.meeting_id;
            if (meetingId && occurrenceDate === currentDate) {
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
            // Use the same batch fetch as in pollActiveMeetings
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
                    if (state.is_running) {
                      hasActive = true;
                      const matching = meetingIdsToCheck.find(m => m.meetingId === state.meeting_id);
                      if (matching && String($('#eventId').val()) === String(matching.eventId) && $('#eventEditModal').hasClass('show')) {
                        this.updateParticipantUI(matching.eventId, state.participant_count, state.is_running, matching.occurrenceDate);
                      }
                      const event = this.calendar.getEventById(matching.eventId);
                      if (event) {
                        event.setExtendedProp('isRunning', true);
                      }
                    }
                  });
                  this.calendar.render();
                }
              },
              error: () => {
                console.warn('checkAndRestoreActiveMeetings - Failed to fetch meeting states');
              }
            });
          }

          if (hasActive) {
            this.hasActiveMeetings = true;
            this.pollActiveMeetings(); // Démarre le polling si des actifs sont trouvés
          } else {
            this.hasActiveMeetings = false;
          }
        }
        csrfHash = data.csrf.csrfHash;
      } catch (e) {
        console.error('checkAndRestoreActiveMeetings - Error:', e);
      }
    },
    error: () => {
      console.error('checkAndRestoreActiveMeetings - AJAX error');
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
// --- Participants Picker Engine ---
_ppData: { create: [], edit: [] },
_ppSelected: { create: [], edit: [] },

refreshUsersDropdown(schoolId, participants, callback) {
    $.ajax({
        url: '<?php echo site_url('admin/get_school_data'); ?>',
        type: 'POST',
        data: { school_id: schoolId, participants: JSON.stringify(participants), [csrfName]: csrfHash },
        success: (response) => {
            try {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    const isCreate = $('#createEventModal').hasClass('show');
                    const mode = isCreate ? 'create' : 'edit';
                    const roleTranslations = {
                        'student': '<?php echo get_phrase("student"); ?>',
                        'teacher': '<?php echo get_phrase("mentor"); ?>',
                        'admin': '<?php echo get_phrase("admin"); ?>',
                        'superadmin': '<?php echo get_phrase("superadmin"); ?>'
                    };
                    const items = [];
                    if (data.classes && data.classes.length > 0) {
                        data.classes.forEach(cls => {
                            items.push({ val: 'class__' + cls.id, type: 'class', label: cls.name, role: '<?php echo get_phrase("class"); ?>', group: 'classes' });
                        });
                    }
                    if (data.users && data.users.length > 0) {
                        data.users.forEach(user => {
                            const roleLabel = roleTranslations[user.role] || (user.role ? user.role.charAt(0).toUpperCase() + user.role.slice(1) : '');
                            items.push({ val: 'individual__' + user.id, type: 'individual', label: user.name, role: roleLabel, group: 'users' });
                        });
                    }
                    CalendarApp._ppData[mode] = items;
                    // Restore selected
                    const selectedVals = participants.map(p => p.type + '__' + p.id);
                    CalendarApp._ppSelected[mode] = selectedVals;
                    CalendarApp.renderPicker(mode);
                    csrfHash = data.csrf.csrfHash;
                    if (typeof callback === 'function') callback();
                } else {
                    if (typeof callback === 'function') callback();
                }
            } catch (e) {
                CalendarApp.showNotification('error', "Error parsing school data");
                if (typeof callback === 'function') callback();
            }
        },
        error: () => { if (typeof callback === 'function') callback(); }
    });
},

renderPicker(mode) {
    const isCreate = mode === 'create';
    const $field = isCreate ? $('#createPPField') : $('#editPPField');
    const $dropdown = isCreate ? $('#participantsDropdown') : $('#editParticipantsDropdown');
    const $hidden = isCreate ? $('#participantsInput') : $('#editParticipantsInput');
    const $search = $field.find('.pp-input');
    const items = this._ppData[mode];
    const selected = this._ppSelected[mode];
    const searchTerm = ($search.val() || '').toLowerCase().trim();

    // Render chips in field
    $field.find('.pp-chip').remove();
    selected.forEach(val => {
        const item = items.find(i => i.val === val);
        if (item) {
            $('<span class="pp-chip" data-val="' + val + '">' +
                '<span class="pp-chip-text">' + this.escapeHtml(item.label) + '</span>' +
                '<span class="pp-chip-x" data-val="' + val + '">&times;</span>' +
            '</span>').insertBefore($search);
        }
    });
    $search.attr('placeholder', selected.length > 0 ? '' : (isCreate ? '<?php echo get_phrase("invite_attendees"); ?>' : '<?php echo get_phrase("Search classes or users"); ?>'));

    // Render dropdown
    let html = '';
    const classItems = items.filter(i => i.group === 'classes');
    const userItems = items.filter(i => i.group === 'users');

    const renderGroup = (groupLabel, groupItems) => {
        const filtered = groupItems.filter(i => !searchTerm || i.label.toLowerCase().includes(searchTerm) || i.role.toLowerCase().includes(searchTerm));
        if (filtered.length === 0) return '';
        let h = '<div class="pp-group">' + groupLabel + '</div>';
        filtered.forEach(item => {
            const checked = selected.includes(item.val) ? ' pp-checked' : '';
            h += '<div class="pp-item' + checked + '" data-val="' + item.val + '">' +
                    '<span class="pp-check-icon"></span>' +
                    '<span class="pp-label">' + this.escapeHtml(item.label) + '</span>' +
                    (item.role ? '<span class="pp-role">' + this.escapeHtml(item.role) + '</span>' : '') +
                 '</div>';
        });
        return h;
    };

    html += renderGroup('<?php echo get_phrase("Classes"); ?>', classItems);
    html += renderGroup('<?php echo get_phrase("Users"); ?>', userItems);

    if (!html) {
        html = '<div class="pp-empty"><?php echo get_phrase("no_results_found"); ?></div>';
    }
    $dropdown.html(html);

    // Update hidden input
    const selectedData = selected.map(val => {
        const parts = val.split('__');
        return { type: parts[0], id: parts[1] };
    });
    $hidden.val(JSON.stringify(selectedData));
},

togglePickerItem(mode, val) {
    const sel = this._ppSelected[mode];
    const idx = sel.indexOf(val);
    if (idx > -1) {
        sel.splice(idx, 1);
    } else {
        sel.push(val);
    }
    this.renderPicker(mode);
},

resetPicker(mode) {
    this._ppSelected[mode] = [];
    const isCreate = mode === 'create';
    const $field = isCreate ? $('#createPPField') : $('#editPPField');
    $field.find('.pp-chip').remove();
    $field.find('.pp-input').val('');
    (isCreate ? $('#participantsDropdown') : $('#editParticipantsDropdown')).html('');
    (isCreate ? $('#participantsInput') : $('#editParticipantsInput')).val('[]');
    (isCreate ? $('#createPPicker') : $('#editPPicker')).removeClass('pp-open');
}
};

$(document).ready(() => {

  // --- Participants Picker Event Handlers ---
  // Open picker on field click
  $(document).on('click', '.pp-field', function() {
      const $picker = $(this).closest('.pp-picker');
      $picker.addClass('pp-open pp-focus');
      $(this).find('.pp-input').focus();
  });

  // Close picker on outside click
  $(document).on('mousedown', function(e) {
      $('.pp-picker.pp-open').each(function() {
          if (!$(this).is(e.target) && !$(this).has(e.target).length) {
              $(this).removeClass('pp-open pp-focus');
          }
      });
  });

  // Search input
  $(document).on('input', '.pp-input', function() {
      const $picker = $(this).closest('.pp-picker');
      $picker.addClass('pp-open');
      const mode = $picker.attr('id') === 'createPPicker' ? 'create' : 'edit';
      CalendarApp.renderPicker(mode);
  });

  // Toggle item in dropdown
  $(document).on('click', '.pp-item', function() {
      const val = $(this).data('val');
      const $picker = $(this).closest('.pp-picker');
      const mode = $picker.attr('id') === 'createPPicker' ? 'create' : 'edit';
      CalendarApp.togglePickerItem(mode, val);
      $picker.find('.pp-input').focus();
  });

  // Remove chip
  $(document).on('click', '.pp-chip-x', function(e) {
      e.stopPropagation();
      const val = $(this).data('val');
      const $picker = $(this).closest('.pp-picker');
      const mode = $picker.attr('id') === 'createPPicker' ? 'create' : 'edit';
      CalendarApp.togglePickerItem(mode, val);
  });

  // Backspace to remove last chip
  $(document).on('keydown', '.pp-input', function(e) {
      if (e.key === 'Backspace' && !$(this).val()) {
          const $picker = $(this).closest('.pp-picker');
          const mode = $picker.attr('id') === 'createPPicker' ? 'create' : 'edit';
          if (CalendarApp._ppSelected[mode].length > 0) {
              CalendarApp._ppSelected[mode].pop();
              CalendarApp.renderPicker(mode);
          }
      }
  });

  const loadScripts = async () => {
    const scripts = [
      'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js',
      'https://cdn.jsdelivr.net/npm/sweetalert2@11',
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

$('#editEventBtn').on('click', () => {
  $('#eventEditModalLabel').text('<?php echo get_phrase("Edit_event"); ?>');
  $('#eventDetailsView').hide();
  $('#eventForm').show();

  // Generate time options based on selected date
  const selectedDate = $('#eventDate').val() || CalendarApp.formatDate(new Date());
  CalendarApp.generateTimeOptions($('#eventStartTime'), selectedDate, true);
  CalendarApp.generateTimeOptions($('#eventEndTime'), selectedDate, true);

  // Retrieve current values or fallback to temporary inputs
  let eventStartTime = $('#eventStartTime').val();
  let eventEndTime = $('#eventEndTime').val();

  // If values are empty, use temporary inputs
  if (!eventStartTime || !eventEndTime) {
    eventStartTime = $('#tempStartTime').val() || '';
    eventEndTime = $('#tempEndTime').val() || '';
    $('#eventStartTime').val(eventStartTime);
    $('#eventEndTime').val(eventEndTime);
  }

  // Update recurrence UI
  const recurrenceType = $('#recurrenceType').val();
  $('#recurrenceTypePopup').val(recurrenceType);
  $('#recurrenceEndDatePopup').val($('#recurrenceEndDate').val());
  $('#customRecurrencePopup').val($('#customRecurrence').val());
  $('#recurrenceStartDateSection').toggle(recurrenceType === 'monthly' || recurrenceType === 'yearly');

  if (recurrenceType === 'weekly' && $('#customRecurrence').val()) {
    try {
      CalendarApp.selectedDays = JSON.parse($('#customRecurrence').val()) || [];
      $('.day-btn').removeClass('active');
      CalendarApp.selectedDays.forEach(day => {
        $(`.day-btn[data-day="${day}"]`).addClass('active');
      });
      $('#daySelection').show();
    } catch (e) {
      console.error('Error parsing custom_recurrence:', e);
    }
  } else if (recurrenceType === 'daily') {
    CalendarApp.selectedDays = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
    $('.day-btn').addClass('active');
    $('#daySelection').show();
  } else {
    CalendarApp.selectedDays = [];
    $('.day-btn').removeClass('active');
    $('#daySelection').hide();
  }
});
});
</script>