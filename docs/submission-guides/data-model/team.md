# Team

**Teams** facilitate collaboration with others on projects by simplifying sharing options. Any user in **[nmrXiv](https://nmrxiv.org/)** can create or join teams.

## Team View
Teams have their own **Dashboards** that look like the main user **Dashboard**, but still, there are a few differences. 

<p align="center">
<img src="/img/team/view.png" width="1000"/>
<figcaption>Shared team view</figcaption>
</p>


Starting from the top left, one can see the team **name** (DOCS), which appears instead of (YOUR DASHBOARD) that is shown in the main user's dashboard, along with avatars of the users who are members of the team as circles with either their images or names' first letters. Going to the left, there are the `TEAM SETTINGS`, which will be explored more in [Edit section](#edit). Moving down, you can see a list of all the teams' projects with a quick view of the project's **name**, **privacy**, and dates of **creating and updating**. You can also see if the project is **starred**. 

:::info
There is a difference between the team creator's dashboard view and the members',  as members will have a greenly-highlighted text mentioning by whom the projects were shared, which is always the team creator, **regardless of who created the project**.
:::

## Create
You can create as many teams as you need by clicking on the user's name at the top right corner to open a menu and click `Create New Team`. 

<p align="center">
<img src="/img/team/create.png" width="1000"/>
<figcaption>Creating a new team from user's menu</figcaption>
</p>


It will lead to a new page that confirms your team ownership and requests a name for it. Then you click `CREATE`, and it is done. Then you get forwarded to the team dashboard. However, you still need to [Edit](#edit) the team to add members.

<p align="center">
<img src="/img/team/create-page.png" width="1000"/>
<figcaption>Create new team page</figcaption>
</p>


## Edit - Team Settings

Once created, you can control the **Team** through **Team Settings**, which can be found at the top right of the page. The **Team** creator is the only one who can change the team settings., although the rest of the members can view the settings.

### Team Name
This section includes details on **The Team's Name and Owner Information**.
The **Team** creator is the only person who can change the **Team**'s name in the field  **Team Name**, and then they press `SAVE`. The email of the **Team** creator is also shown here, but it can't be modified.

<p align="center">
<img src="/img/team/name.png" width="1000"/>
<figcaption> Team Name section in the Team Settings</figcaption>
</p>


### Add Team Member
If you are the creator of the **Team**, you can invite people to join the **Team** and grant them specific roles. You have to enter the invitee's email and pick their role from the provided list, and press `ADD`. The possible roles are:

1. **Owner**: Provides full control of the projects. They can read and/or update, including deleting, the project, study, and dataset.
2. **Collaborator**: They can read and/or update the project, study, and dataset.
3. **Reviewer**: They can only read the project, study, and dataset.

:::info
Please note that one **Team** can have many owners with the same permissions to control **Projects** and **Studies**. However, only the **Team** creator has the right to change the settings of the **Team**.

**Team** creator is a **Team** owner by default.
:::

<p align="center">
<img src="/img/team/add-member.png" width="1000"/>
<figcaption>Add Team Member section in the Team Settings</figcaption>
</p>


### Pending Team Invitations
This section appears only when there are pending invitations and only to the **Team** creator. It enables them to `Cancel` invitations before they are accepted.

<p align="center">
<img src="/img/team/pending.png" width="1000"/>
<figcaption>Pending Team Invitations section in the Team Settings</figcaption>
</p>


The invitee will receive the invitation via email. The invitation has two buttons, one to `Accept Invitation` and another to `Create Account` if the invitee doesn't have one already. In this second case, the invitee can go back to the invitation to accept it after creating the account.

<p align="center">
<img src="/img/team/invitation.png" width="1000"/>
<figcaption>Invitation email to join team on nmrXiv</figcaption>
</p>


###  Team Members 
This section shows a list of all the **Team** members (except the creator). Each member is present with the following details:
* Member's name: As provided to **[nmrXiv](https://nmrxiv.org/)**.
* Member's role: The **Team** creator can manage the members' roles here by clicking on the shown role and picking another one, and then either `SAVE` the changes or `CANCEL` them.
* Remove: So that the **Team** creator can `Remove` members from the **Team**.

<p align="center">
<img src="/img/team/member.png" width="1000"/>
<figcaption>Team Members section in the Team Settings</figcaption>
</p>


This section appears to the rest of the team members too, but with a different view. All the entries are not editable, and the `Remove` option is replaced by `Leave`. 

<p align="center">
<img src="/img/team/member-view.png" width="1000"/>
<figcaption>Team Settings view for teams members</figcaption>
</p>


## Delete and Archive
This option permanently deletes the **Team** with all its data and private resources. We recommend downloading the data before deleting it. However, if the team has some public resources, they get archived instead of deleted.
<p align="center">
<img src="/img/team/delete.png" width="1000"/>
<figcaption>Delete Team section in the Team Settings</figcaption>
</p>


## More on Ownerships
Ownership can get complicated within a team. Here are some cases you might want to be aware of:
- Team **creator** is also assigned a **creator** ownership of all the team resources, regardless of who actually uploaded the data.
- Members who submit projects are **owners** of those projects.
- If a member is **collaborator** or a **reviewer** in a team and they submit a project there, they become an **owner** of the project, even if they have a lower role in the team.