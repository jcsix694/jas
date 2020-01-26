# JAS

JAS (Job Application System) is an API which manages job applications for a company. Admins have the ability to manage jobs, shifts and applications whilst workers can view available shifts and apply for new shifts.


# Usage

Pleases send all requests with the following headers:

> accept: application/json

A default admin is configured with the following details:

> username: admin@mail.com
> password: password

Use these credentials to login using the login endpoing to retrieve the Bearer Token.

> POST /api/login
> 
> {
  "token_type": "Bearer",
  "expires_in": 31622400,
  "access_token": "",
  "refresh_token": ""
}

Once logged in new admins can be created with the following endpoint:

> POST /api/admin

Alternatively, register as a worker with the following endpoint:

> POST /api/register

Then use the login endpoint to retrieve the Bearer Token.

# Endpoints

|Method|Endpoint|Description|Query Filters  |
|--|--|--|--|
| POST |api/login|Returns Bearer Token to be used with API Calls  |  |
| POST |api/register|Register as a worker  |  |

## Admin

|Method|Endpoint|Description|Query Filters  |
|--|--|--|--|
| POST |api/admin  |Cretes an admin  |  |
| GET|api/admin  |Gets a list of admins  | id |
| GET |api/worker|Gets a list of workes| id |
| GET|api/user|Gets user  |  |
| POST |api/job|Cretes a job|  |
| GET|api/job|Gets a list of jobs  | id |
| POST |api/shift|Cretes a shift of a job |  |
| GET|api/shift  |Gets a list of shifts  | id, job_id |
| GET|api/shift/available|Gets a list of available shifts  |id, job_id  |
| GET|api/application/statuses  |Gets a list of statuses  |  |
| POST |api/application/accept|Accepts an application  |  |
| POST |api/application/decline|Declines an application  |  |
| GET|api/application|Gets a list of applications  |is, status_id  |


## Workers

|Method|Endpoint|Description|Query Filters  |
|--|--|--|--|
| GET|api/user|Gets user  |  |
| GET|api/shift  |Gets shift assigned to worker  |  |
| GET|api/shift/available|Gets a list of available shifts  |id, job_id  |
| GET|api/application/statuses  |Gets a list of statuses  |  |
| POST |api/application|Creates an application for a shift  |  |
| DELETE|api/application|Deletes an application  |  |
| GET|api/application|Gets a list of applications created by the worker  |is, status_id  |
