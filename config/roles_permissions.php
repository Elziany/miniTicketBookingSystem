<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Master Permissions Map
    |--------------------------------------------------------------------------
    |
    | Central definition of all system permissions grouped by domain module.
    |
    */

    'permissions' => [
        // Users
        'view_user' => 'view_user',
        'view_any_user' => 'view_any_user',
        'create_user' => 'create_user',
        'edit_user' => 'edit_user',
        'delete_user' => 'delete_user',

        // Roles & Permissions
        'view_role' => 'view_role',
        'view_any_role' => 'view_any_role',
        'create_role' => 'create_role',
        'edit_role' => 'edit_role',
        'delete_role' => 'delete_role',
        'view_permission' => 'view_permission',
        'manage_permission' => 'manage_permission',

        // System Settings
        'view_setting' => 'view_setting',
        'edit_setting' => 'edit_setting',

        // Halls
        'view_hall' => 'view_hall',
        'view_any_hall' => 'view_any_hall',
        'create_hall' => 'create_hall',
        'edit_hall' => 'edit_hall',
        'delete_hall' => 'delete_hall',

        // Seats
        'view_seat' => 'view_seat',
        'view_any_seat' => 'view_any_seat',
        'create_seat' => 'create_seat',
        'edit_seat' => 'edit_seat',
        'delete_seat' => 'delete_seat',

        // Agents / Hierarchy
        'view_agent' => 'view_agent',
        'view_any_agent' => 'view_any_agent',
        'create_agent' => 'create_agent',
        'edit_agent' => 'edit_agent',
        'delete_agent' => 'delete_agent',
        'assign_agent_hall' => 'assign_agent_hall',
        'manage_agent_hierarchy' => 'manage_agent_hierarchy',

        // Events
        'view_event' => 'view_event',
        'view_any_event' => 'view_any_event',
        'create_event' => 'create_event',
        'edit_event' => 'edit_event',
        'reschedule_event' => 'reschedule_event',
        'delete_event' => 'delete_event',
        'publish_event' => 'publish_event',
        'cancel_event' => 'cancel_event',
        'start_event' => 'start_event',
        'end_event' => 'end_event',

        // Pricing
        'view_event_price' => 'view_event_price',
        'create_event_price' => 'create_event_price',
        'edit_event_price' => 'edit_event_price',
        'delete_event_price' => 'delete_event_price',

        // Reservations
        'view_reservation' => 'view_reservation',
        'view_any_reservation' => 'view_any_reservation',
        'create_reservation' => 'create_reservation',
        'edit_reservation' => 'edit_reservation',
        'cancel_reservation' => 'cancel_reservation',
        'view_pending_reservation' => 'view_pending_reservation',
        'approve_reservation' => 'approve_reservation',
        'reject_reservation' => 'reject_reservation',

        // Orders
        'view_order' => 'view_order',
        'view_any_order' => 'view_any_order',
        'create_order' => 'create_order',
        'cancel_order' => 'cancel_order',
        'refund_order' => 'refund_order',

        // Attendance
        'view_attendance' => 'view_attendance',
        'scan_attendance' => 'scan_attendance',
        'manual_attendance' => 'manual_attendance',
        'view_any_attendance' => 'view_any_attendance',
        'create_attendance' => 'create_attendance',
        'edit_attendance' => 'edit_attendance',

        // Notifications
        'view_notification' => 'view_notification',
        'send_notification' => 'send_notification',

        // Feedback
        'view_feedback' => 'view_feedback',
        'view_any_feedback' => 'view_any_feedback',
        'create_feedback_question' => 'create_feedback_question',
        'edit_feedback_question' => 'edit_feedback_question',
        'delete_feedback_question' => 'delete_feedback_question',
        'create_feedback' => 'create_feedback',
        'edit_feedback' => 'edit_feedback',
        'view_feedback_question' => 'view_feedback_question' ,
        'view_any_feedback_question' => 'view_any_feedback_question',


        // Audit
        'view_audit_log' => 'view_audit_log',
        'view_any_audit_log' => 'view_any_audit_log',
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Assignments
    |--------------------------------------------------------------------------
    |
    | Maps each role to its specific permission array.
    |
    */

    'role_permissions' => [

        'Admin' => [
            'view_user', 'view_any_user', 'create_user', 'edit_user', 'delete_user',
            'view_role', 'view_any_role', 'create_role', 'edit_role', 'delete_role', 'view_permission', 'manage_permission',
            'view_setting', 'edit_setting',

            'view_hall', 'view_any_hall', 'create_hall', 'edit_hall', 'delete_hall',
            'view_seat', 'view_any_seat', 'create_seat', 'edit_seat', 'delete_seat',

            'view_agent', 'view_any_agent', 'create_agent', 'edit_agent', 'delete_agent', 'assign_agent_hall', 'manage_agent_hierarchy',

            'view_event', 'view_any_event', 'create_event', 'edit_event', 'reschedule_event', 'delete_event', 'publish_event', 'cancel_event', 'start_event', 'end_event',
            'view_event_price', 'create_event_price', 'edit_event_price', 'delete_event_price',

            'view_reservation', 'view_any_reservation', 'create_reservation', 'edit_reservation', 'cancel_reservation', 'view_pending_reservation', 'approve_reservation', 'reject_reservation',
            'view_order', 'view_any_order', 'create_order', 'cancel_order', 'refund_order',

            'view_attendance', 'view_any_attendance', 'scan_attendance', 'manual_attendance',

            'view_notification', 'send_notification',

            'view_feedback', 'view_any_feedback', 'create_feedback' , 'edit_feedback','view_feedback_question' , 'view_any_feedback_question'  ,'create_feedback_question', 'edit_feedback_question', 'delete_feedback_question',

            // Audit
            'view_audit_log', 'view_any_audit_log',
            'create_attendance' , 'edit_attendance'
        ],

        'Hall Manager' => [
            'view_hall', 'view_any_hall', 'edit_hall',
            'view_seat', 'view_any_seat', 'create_seat', 'edit_seat',
            'view_agent', 'view_any_agent', 'assign_agent_hall', 'manage_agent_hierarchy',
            'view_event', 'view_any_event', 'create_event', 'edit_event', 'reschedule_event', 'publish_event', 'cancel_event', 'start_event', 'end_event',
            'view_event_price', 'create_event_price', 'edit_event_price',
            'view_reservation', 'view_any_reservation', 'view_pending_reservation', 'approve_reservation', 'reject_reservation', 'cancel_reservation',
            'view_order', 'view_any_order', 'cancel_order', 'refund_order',
            'view_attendance', 'scan_attendance', 'manual_attendance', 'view_any_attendance',
            'view_notification', 'send_notification',
            'view_feedback', 'view_any_feedback', 'create_feedback_question', 'edit_feedback_question', 'delete_feedback_question',
            'view_audit_log', 'view_any_audit_log',
        ],

        'Event Manager' => [
            'view_hall', 'view_any_hall', 'view_seat', 'view_any_seat',
            'view_event', 'view_any_event', 'create_event', 'edit_event', 'reschedule_event', 'delete_event', 'publish_event', 'cancel_event', 'start_event', 'end_event',
            'view_event_price', 'create_event_price', 'edit_event_price', 'delete_event_price',
            'view_reservation', 'view_any_reservation',
            'view_order', 'view_any_order',
            'view_feedback', 'view_any_feedback', 'create_feedback_question', 'edit_feedback_question', 'delete_feedback_question',
        ],

        'Agent' => [
            'view_event', 'view_any_event',
            'view_hall', 'view_seat', 'view_any_seat',
            'view_agent',
            'view_reservation', 'view_any_reservation', 'view_pending_reservation', 'approve_reservation', 'reject_reservation', 'cancel_reservation',
            'view_order', 'view_any_order',
            'view_attendance', 'scan_attendance', 'manual_attendance', 'view_any_attendance',
        ],
    ],
];