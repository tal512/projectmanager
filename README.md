# projectmanager

A web-based project management tool for developers.

V1 goals:
- user crud & authentication
- project crud & ownership
- task crud

## User
A user is identified by EMAIL and PASSWORD. Users are either ADMINs or MEMBERs. An admin can create, update and delete other users and do everything a member can. A member can create, update and delete projects and tasks.

## Project
A project is a container for tasks created by users. It is identified by NAME and OWNED by the user who created it, or by the user the previous owner or an admin transferred the ownership to. The project owner or an admin can add other users to the project as COLLABORATORS, who can create, update and delete tasks belonging to the project.

## Task
A task is a description of something that needs to be done as part of a project. It is defined by ID NUMBER, SHORT DESCRIPTION and LONG DESCRIPTION, and can be ASSIGNED to a collaborator. A task can be RELATED to a project or another task, allowing it to describe the necessary action or feature in increasing detail. Tasks related to the same project or parent task can be PRIORITIZED in relation to one another by the collaborators.
