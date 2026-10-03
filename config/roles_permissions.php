<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Master Permissions Map
    |--------------------------------------------------------------------------
    |
    | Shield-generated CRUD permissions use:
    |
    | ViewAny:Model
    | View:Model
    | Create:Model
    | Update:Model
    | Delete:Model
    |
    | Custom business permissions use the same convention:
    |
    | Publish:Event
    | Approve:Reservation
    | Refund:Order
    |
    */

    'permissions' => [

        /*
        |--------------------------------------------------------------------------
        | Users
        |--------------------------------------------------------------------------
        */

        'ViewAny:User' => 'ViewAny:User',
        'View:User' => 'View:User',
        'Create:User' => 'Create:User',
        'Update:User' => 'Update:User',
        'Delete:User' => 'Delete:User',

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        'ViewAny:Role' => 'ViewAny:Role',
        'View:Role' => 'View:Role',
        'Create:Role' => 'Create:Role',
        'Update:Role' => 'Update:Role',
        'Delete:Role' => 'Delete:Role',

        /*
        |--------------------------------------------------------------------------
        | Permissions
        |--------------------------------------------------------------------------
        */

        'ViewAny:Permission' => 'ViewAny:Permission',
        'View:Permission' => 'View:Permission',
        'Create:Permission' => 'Create:Permission',
        'Update:Permission' => 'Update:Permission',
        'Delete:Permission' => 'Delete:Permission',

        'Manage:Permission' => 'Manage:Permission',

        /*
        |--------------------------------------------------------------------------
        | System Configurations
        |--------------------------------------------------------------------------
        */

        'ViewAny:SystemConfiguration' => 'ViewAny:SystemConfiguration',
        'View:SystemConfiguration' => 'View:SystemConfiguration',
        'Create:SystemConfiguration' => 'Create:SystemConfiguration',
        'Update:SystemConfiguration' => 'Update:SystemConfiguration',
        'Delete:SystemConfiguration' => 'Delete:SystemConfiguration',

        /*
        |--------------------------------------------------------------------------
        | Halls
        |--------------------------------------------------------------------------
        */

        'ViewAny:Hall' => 'ViewAny:Hall',
        'View:Hall' => 'View:Hall',
        'Create:Hall' => 'Create:Hall',
        'Update:Hall' => 'Update:Hall',
        'Delete:Hall' => 'Delete:Hall',

        /*
        |--------------------------------------------------------------------------
        | Seats
        |--------------------------------------------------------------------------
        */

        'ViewAny:Seat' => 'ViewAny:Seat',
        'View:Seat' => 'View:Seat',
        'Create:Seat' => 'Create:Seat',
        'Update:Seat' => 'Update:Seat',
        'Delete:Seat' => 'Delete:Seat',

        /*
        |--------------------------------------------------------------------------
        | Agents
        |--------------------------------------------------------------------------
        |
        | Add these when AgentResource exists.
        |
        */

        'ViewAny:Agent' => 'ViewAny:Agent',
        'View:Agent' => 'View:Agent',
        'Create:Agent' => 'Create:Agent',
        'Update:Agent' => 'Update:Agent',
        'Delete:Agent' => 'Delete:Agent',

        /*
        |--------------------------------------------------------------------------
        | Agent Business Permissions
        |--------------------------------------------------------------------------
        */

        'Assign:AgentHall' => 'Assign:AgentHall',
        'Manage:AgentHierarchy' => 'Manage:AgentHierarchy',

        /*
        |--------------------------------------------------------------------------
        | Events
        |--------------------------------------------------------------------------
        */

        'ViewAny:Event' => 'ViewAny:Event',
        'View:Event' => 'View:Event',
        'Create:Event' => 'Create:Event',
        'Update:Event' => 'Update:Event',
        'Delete:Event' => 'Delete:Event',

        /*
        |--------------------------------------------------------------------------
        | Event Business Permissions
        |--------------------------------------------------------------------------
        */

        'Reschedule:Event' => 'Reschedule:Event',
        'Publish:Event' => 'Publish:Event',
        'Cancel:Event' => 'Cancel:Event',
        'Start:Event' => 'Start:Event',
        'End:Event' => 'End:Event',

        /*
        |--------------------------------------------------------------------------
        | Event Seat Prices
        |--------------------------------------------------------------------------
        */

        'ViewAny:EventSeatPrice' => 'ViewAny:EventSeatPrice',
        'View:EventSeatPrice' => 'View:EventSeatPrice',
        'Create:EventSeatPrice' => 'Create:EventSeatPrice',
        'Update:EventSeatPrice' => 'Update:EventSeatPrice',
        'Delete:EventSeatPrice' => 'Delete:EventSeatPrice',

        /*
        |--------------------------------------------------------------------------
        | Reservations
        |--------------------------------------------------------------------------
        */

        'ViewAny:Reservation' => 'ViewAny:Reservation',
        'View:Reservation' => 'View:Reservation',
        'Create:Reservation' => 'Create:Reservation',
        'Update:Reservation' => 'Update:Reservation',
        'Delete:Reservation' => 'Delete:Reservation',

        /*
        |--------------------------------------------------------------------------
        | Reservation Business Permissions
        |--------------------------------------------------------------------------
        */

        'Cancel:Reservation' => 'Cancel:Reservation',
        'ViewPending:Reservation' => 'ViewPending:Reservation',
        'Approve:Reservation' => 'Approve:Reservation',
        'Reject:Reservation' => 'Reject:Reservation',

        /*
        |--------------------------------------------------------------------------
        | Orders
        |--------------------------------------------------------------------------
        */

        'ViewAny:Order' => 'ViewAny:Order',
        'View:Order' => 'View:Order',
        'Create:Order' => 'Create:Order',
        'Update:Order' => 'Update:Order',
        'Delete:Order' => 'Delete:Order',

        /*
        |--------------------------------------------------------------------------
        | Order Business Permissions
        |--------------------------------------------------------------------------
        */

        'Cancel:Order' => 'Cancel:Order',
        'Refund:Order' => 'Refund:Order',

        /*
        |--------------------------------------------------------------------------
        | Attendance
        |--------------------------------------------------------------------------
        */

        'ViewAny:Attendance' => 'ViewAny:Attendance',
        'View:Attendance' => 'View:Attendance',
        'Create:Attendance' => 'Create:Attendance',
        'Update:Attendance' => 'Update:Attendance',
        'Delete:Attendance' => 'Delete:Attendance',

        /*
        |--------------------------------------------------------------------------
        | Attendance Business Permissions
        |--------------------------------------------------------------------------
        */

        'Scan:Attendance' => 'Scan:Attendance',
        'Manual:Attendance' => 'Manual:Attendance',

        /*
        |--------------------------------------------------------------------------
        | Event Feedback
        |--------------------------------------------------------------------------
        */

        'ViewAny:EventFeedback' => 'ViewAny:EventFeedback',
        'View:EventFeedback' => 'View:EventFeedback',
        'Create:EventFeedback' => 'Create:EventFeedback',
        'Update:EventFeedback' => 'Update:EventFeedback',
        'Delete:EventFeedback' => 'Delete:EventFeedback',

        /*
        |--------------------------------------------------------------------------
        | Feedback Questions
        |--------------------------------------------------------------------------
        */

        'ViewAny:FeedbackQuestion' => 'ViewAny:FeedbackQuestion',
        'View:FeedbackQuestion' => 'View:FeedbackQuestion',
        'Create:FeedbackQuestion' => 'Create:FeedbackQuestion',
        'Update:FeedbackQuestion' => 'Update:FeedbackQuestion',
        'Delete:FeedbackQuestion' => 'Delete:FeedbackQuestion',

        /*
        |--------------------------------------------------------------------------
        | Feedback
        |--------------------------------------------------------------------------
        */

        'ViewAny:Feedback' => 'ViewAny:Feedback',
        'View:Feedback' => 'View:Feedback',
        'Create:Feedback' => 'Create:Feedback',
        'Update:Feedback' => 'Update:Feedback',
        'Delete:Feedback' => 'Delete:Feedback',

        /*
        |--------------------------------------------------------------------------
        | Audit Logs
        |--------------------------------------------------------------------------
        */

        'ViewAny:AuditLog' => 'ViewAny:AuditLog',
        'View:AuditLog' => 'View:AuditLog',
        'Create:AuditLog' => 'Create:AuditLog',
        'Update:AuditLog' => 'Update:AuditLog',
        'Delete:AuditLog' => 'Delete:AuditLog',

        /*
        |--------------------------------------------------------------------------
        | Notifications
        |--------------------------------------------------------------------------
        */

        'ViewAny:Notification' => 'ViewAny:Notification',
        'View:Notification' => 'View:Notification',
        'Create:Notification' => 'Create:Notification',
        'Update:Notification' => 'Update:Notification',
        'Delete:Notification' => 'Delete:Notification',

        'Send:Notification' => 'Send:Notification',
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Permissions
    |--------------------------------------------------------------------------
    |
    | These roles use the exact permission names checked by the Policies.
    |
    */

    'role_permissions' => [

        /*
        |--------------------------------------------------------------------------
        | Admin
        |--------------------------------------------------------------------------
        */

        'Admin' => [

            // Users
            'ViewAny:User',
            'View:User',
            'Create:User',
            'Update:User',
            'Delete:User',

            // Roles
            'ViewAny:Role',
            'View:Role',
            'Create:Role',
            'Update:Role',
            'Delete:Role',

            // Permissions
            'ViewAny:Permission',
            'View:Permission',
            'Create:Permission',
            'Update:Permission',
            'Delete:Permission',
            'Manage:Permission',

            // System configuration
            'ViewAny:SystemConfiguration',
            'View:SystemConfiguration',
            'Create:SystemConfiguration',
            'Update:SystemConfiguration',
            'Delete:SystemConfiguration',

            // Halls
            'ViewAny:Hall',
            'View:Hall',
            'Create:Hall',
            'Update:Hall',
            'Delete:Hall',

            // Seats
            'ViewAny:Seat',
            'View:Seat',
            'Create:Seat',
            'Update:Seat',
            'Delete:Seat',

            // Agents
            'ViewAny:Agent',
            'View:Agent',
            'Create:Agent',
            'Update:Agent',
            'Delete:Agent',
            'Assign:AgentHall',
            'Manage:AgentHierarchy',

            // Events
            'ViewAny:Event',
            'View:Event',
            'Create:Event',
            'Update:Event',
            'Delete:Event',

            'Reschedule:Event',
            'Publish:Event',
            'Cancel:Event',
            'Start:Event',
            'End:Event',

            // Event seat prices
            'ViewAny:EventSeatPrice',
            'View:EventSeatPrice',
            'Create:EventSeatPrice',
            'Update:EventSeatPrice',
            'Delete:EventSeatPrice',

            // Reservations
            'ViewAny:Reservation',
            'View:Reservation',
            'Create:Reservation',
            'Update:Reservation',
            'Delete:Reservation',

            'Cancel:Reservation',
            'ViewPending:Reservation',
            'Approve:Reservation',
            'Reject:Reservation',

            // Orders
            'ViewAny:Order',
            'View:Order',
            'Create:Order',
            'Update:Order',
            'Delete:Order',

            'Cancel:Order',
            'Refund:Order',

            // Attendance
            'ViewAny:Attendance',
            'View:Attendance',
            'Create:Attendance',
            'Update:Attendance',
            'Delete:Attendance',

            'Scan:Attendance',
            'Manual:Attendance',

            // Event feedback
            'ViewAny:EventFeedback',
            'View:EventFeedback',
            'Create:EventFeedback',
            'Update:EventFeedback',
            'Delete:EventFeedback',

            // Feedback questions
            'ViewAny:FeedbackQuestion',
            'View:FeedbackQuestion',
            'Create:FeedbackQuestion',
            'Update:FeedbackQuestion',
            'Delete:FeedbackQuestion',

            // Feedback
            'ViewAny:Feedback',
            'View:Feedback',
            'Create:Feedback',
            'Update:Feedback',
            'Delete:Feedback',

            // Notifications
            'ViewAny:Notification',
            'View:Notification',
            'Create:Notification',
            'Update:Notification',
            'Delete:Notification',
            'Send:Notification',

            // Audit logs
            'ViewAny:AuditLog',
            'View:AuditLog',
            'Create:AuditLog',
            'Update:AuditLog',
            'Delete:AuditLog',
        ],

        /*
        |--------------------------------------------------------------------------
        | Hall Manager
        |--------------------------------------------------------------------------
        */

        'Hall Manager' => [

            // Halls
            'ViewAny:Hall',
            'View:Hall',
            'Update:Hall',

            // Seats
            'ViewAny:Seat',
            'View:Seat',
            'Create:Seat',
            'Update:Seat',

            // Agents
            'ViewAny:Agent',
            'View:Agent',
            'Assign:AgentHall',
            'Manage:AgentHierarchy',

            // Events
            'ViewAny:Event',
            'View:Event',
            'Create:Event',
            'Update:Event',

            'Reschedule:Event',
            'Publish:Event',
            'Cancel:Event',
            'Start:Event',
            'End:Event',

            // Pricing
            'ViewAny:EventSeatPrice',
            'View:EventSeatPrice',
            'Create:EventSeatPrice',
            'Update:EventSeatPrice',

            // Reservations
            'ViewAny:Reservation',
            'View:Reservation',
            'ViewPending:Reservation',
            'Approve:Reservation',
            'Reject:Reservation',
            'Cancel:Reservation',

            // Orders
            'ViewAny:Order',
            'View:Order',
            'Cancel:Order',
            'Refund:Order',

            // Attendance
            'ViewAny:Attendance',
            'View:Attendance',
            'Scan:Attendance',
            'Manual:Attendance',

            // Notifications
            'ViewAny:Notification',
            'View:Notification',
            'Send:Notification',

            // Feedback
            'ViewAny:Feedback',
            'View:Feedback',

            // Feedback questions
            'ViewAny:FeedbackQuestion',
            'View:FeedbackQuestion',
            'Create:FeedbackQuestion',
            'Update:FeedbackQuestion',
            'Delete:FeedbackQuestion',

            // Audit
            'ViewAny:AuditLog',
            'View:AuditLog',
        ],

        /*
        |--------------------------------------------------------------------------
        | Event Manager
        |--------------------------------------------------------------------------
        */

        'Event Manager' => [

            // Halls
            'ViewAny:Hall',
            'View:Hall',

            // Seats
            'ViewAny:Seat',
            'View:Seat',

            // Events
            'ViewAny:Event',
            'View:Event',
            'Create:Event',
            'Update:Event',
            'Delete:Event',

            'Reschedule:Event',
            'Publish:Event',
            'Cancel:Event',
            'Start:Event',
            'End:Event',

            // Pricing
            'ViewAny:EventSeatPrice',
            'View:EventSeatPrice',
            'Create:EventSeatPrice',
            'Update:EventSeatPrice',
            'Delete:EventSeatPrice',

            // Reservations
            'ViewAny:Reservation',
            'View:Reservation',

            // Orders
            'ViewAny:Order',
            'View:Order',

            // Feedback
            'ViewAny:Feedback',
            'View:Feedback',

            // Feedback questions
            'ViewAny:FeedbackQuestion',
            'View:FeedbackQuestion',
            'Create:FeedbackQuestion',
            'Update:FeedbackQuestion',
            'Delete:FeedbackQuestion',
        ],

        /*
        |--------------------------------------------------------------------------
        | Agent
        |--------------------------------------------------------------------------
        */

        'Agent' => [

            // Events
            'ViewAny:Event',
            'View:Event',

            // Halls
            'View:Hall',

            // Seats
            'ViewAny:Seat',
            'View:Seat',

            // Agent
            'View:Agent',

            // Reservations
            'ViewAny:Reservation',
            'View:Reservation',
            'ViewPending:Reservation',
            'Approve:Reservation',
            'Reject:Reservation',
            'Cancel:Reservation',

            // Orders
            'ViewAny:Order',
            'View:Order',

            // Attendance
            'ViewAny:Attendance',
            'View:Attendance',
            'Scan:Attendance',
            'Manual:Attendance',
        ],
    ],
];
