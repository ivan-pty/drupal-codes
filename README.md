# Description.

Menus:
- Menu to collect the custom menu items for the site.
  - path: '/admin/config/nene'

- Dashboard for the parents:
  - path: '/my-dashboard'
    
- Form to test Firebase Cloud Messaging(FCM):
  - path: '/admin/config/nene/fcm-notification'
  
Controllers:
- NeneControllerBase: Principal Controller to be used as an extends.
- MyDashboardController: Controller to print the dashboard for the parents(menu items and their appointments)
- AppointmentsController: Controller to print the appointments. 

Forms:
- NeneFcmForm: Custom form the test Firebase Cloud Messaging(FCM)

Plugins:
- Custom REST resources to be used for a third party, it has 3 items:
    - AppoinmentsResource: Resource to get the appoints, for the logged user.
    - FcmResource: Resource to get the list of the available topics.
    - MyTeacherResource: Resource to the the info of his/her teacher.
    
Services:
- NeneApi: Custom functions to be used by the controllers.

Templates:
- nene-my-teacher: Print the info the of teacher.
- nene-parents-menu: Print the menu items available for the parents and their appointments.

Assets:
- menuItems: Function to add the active class when a menu item if clicked.
