Simple REST Api for email sending
========================

API endpoints  
* create mail with multiple recipients, sender, status (to send or sent) and priority.
* get specific mail
* get all mails
* send all unsent emails

Uses Rabbitmq for handling of send email jobs.
Implements MailerInterface so SwiftMailer can be easily replace by other adapter class.