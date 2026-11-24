# OLVM Self-Study: Users and Permissions

## Introduction

This module contains **45 scored practice questions** for the users and permissions objective, which accounts for **9%** of the exam weighting supplied for this training. Use it after the instructor-led Lab 7 session and while reviewing the supplied course transcripts.

Estimated Time: 45-63 minutes, including explanation review

```quiz-config
passing: 80
badge: images/badge.png
```

### Objectives

- Perform Administrator Tasks
- Manage Users and Roles
- Manage Groups
- Manage Account Settings
- Manage Quotas

### How to Practice

Choose **one option per question**, including questions whose choices contain a set of items. Click **Check Answer** to see feedback and the explanation. Answer all 45 questions; at least **36 correct answers** unlock this module's badge. The 80% target is a study milestone, not an official exam passing score.

Record your first-attempt score before using **Try Again**. Review the corresponding transcript clip or Lab 7 task, explain the reasoning, and retry after studying. Scores are tracked within this module; keep your own first-attempt record before navigating away.

The recorded course uses older OL8 and OLVM examples than Lab 7. Questions explicitly referring to the recorded course use that context. Match package, identity, and UI procedures to the actual practice environment.

[Study guide and coverage map](../../workshops/olvm-study/index.html?lab=study-guide)

## Task 1: Perform Administrator Tasks

```quiz score
Q: 5.001. Which administrative task involves creating and managing user accounts with appropriate roles?
* Managing user setup and setting permission levels
- Managing physical resources
- Generating reports
- Monitoring resources
> User setup and permission levels determine who can access OLVM resources and what actions they can perform. This is separate from monitoring or reporting because it controls identity and authorization.
```

```quiz score
Q: 5.002. What are the main categories of resources that administrators manage in OLVM? Select the option listing the complete correct set.
* Physical servers; Virtual resources (VMs, vCPUs, memory, storage, networks)
- Physical servers; Email systems
- Office applications; Virtual resources (VMs, vCPUs, memory, storage, networks)
- Email systems; Virtual resources (VMs, vCPUs, memory, storage, networks)
> OLVM administrators manage both physical servers and virtual resources such as VMs, vCPUs, memory, storage, and networks. The platform spans the physical host layer and the virtual infrastructure layer.
```

```quiz score
Q: 5.003. What should administrators set up for critical resource thresholds?
- Virtual machines
- User accounts
- Firewalls
* Alerts for critical thresholds and anomalies
> Administrators should configure alerts for critical thresholds and anomalies so resource or health issues are noticed early. Alerts support proactive operations rather than waiting for users to report failures.
```

```quiz score
Q: 5.004. What can administrators use to organize and manage OLVM objects?
* Custom tags
- Directories
- Scripts
- Folders
> Custom tags help organize and manage OLVM objects logically. Tags make it easier to group, search, and categorize resources beyond the built-in hierarchy.
```

## Task 2: Manage Users and Roles

```quiz score
Q: 5.005. What are the two types of user domains in OLVM? Select the option listing the complete correct set.
- Cloud domain; External domain
- Local domain; Cloud domain
* Local domain; External domain
- Hybrid domain; External domain
> OLVM supports local domains and external domains. Local domains are internal to OLVM, while external domains connect to directory services such as Active Directory or LDAP.
```

```quiz score
Q: 5.006. Which local authentication profile is used in the recorded legacy user-management demonstrations?
- The VLAN ID pool
- The iSCSI initiator profile
* internal
- The Grafana dashboard folder
> The recorded course uses JDBC-backed internal authentication. Lab 7 also has a Keycloak route, so select procedures that match the actual identity configuration.
```

```quiz score
Q: 5.007. Which account is the initial administrator in the recorded legacy internal profile?
- vdsm
- The name of every newly created guest user
- sanlock
* admin
> The recorded setup uses admin in internal. This identifies the course's legacy setup rather than asserting one identity configuration for all OLVM environments.
```

```quiz score
Q: 5.008. Lab 7 offers Keycloak and legacy JDBC identity paths. How should a learner choose the account procedure?
- Run both procedures and assume they manage the same account store
- Treat a guest OS account as an OLVM account automatically
- Create the account only in the history database
* Use the path matching the engine's actual identity configuration
> Lab 7 Task 10 distinguishes identity configurations. Account creation and password management must target the identity system in use.
```

```quiz score
Q: 5.009. What tool is used to create additional users on the internal domain?
* ovirt-aaa-jdbc-tool
- useradd
- ovirt-user-tool
- engine-user-create
> `ovirt-aaa-jdbc-tool` manages users and groups in the internal domain. Standard Linux `useradd` would create operating system users, not OLVM internal authentication users.
```

```quiz score
Q: 5.010. A Keycloak user can authenticate but has no VM access. Which next step is relevant?
- Make all storage domains local
- Change the iSCSI target's CHAP password
* Assign the appropriate OLVM role and resource permission after selecting the user
- Import the engine backup into the guest
> Lab 7 Task 10 separates the identity provider from OLVM permissions. Authentication does not by itself authorize VM operations.
```

```quiz score
Q: 5.011. Which two external directory servers can be attached to OLVM? Select the option listing the complete correct set.
- Kerberos; OpenLDAP
* Active Directory; OpenLDAP
- Active Directory; NIS
- NIS; OpenLDAP
> OLVM can attach to Active Directory and OpenLDAP as external directory services. These integrations let organizations use existing enterprise identity sources.
```

```quiz score
Q: 5.012. What are users created on external domains called?
- External users
- LDAP users
* Directory users
- Remote users
> Users from external domains are called directory users because their identities are stored and authenticated by the external directory service. OLVM consumes those identities rather than storing the password locally.
```

```quiz score
Q: 5.013. Why integrate OLVM with an organizational directory?
- To automatically grant every directory user full administrator permissions
- To make VM disks shareable by default
* To centralize identity management and apply the intended authentication policies
- To eliminate the need for assigned roles
> The external-domain lesson discusses centralized identity management. Single sign-on depends on its configuration, and authorization still requires appropriate OLVM permissions.
```

```quiz score
Q: 5.014. Where do directory users authenticate?
- At the hypervisor level
- Within OLVM's internal system
* Against their respective external directory servers
- Through the database
> Directory users authenticate against their external directory servers. OLVM delegates authentication to that source and then applies OLVM permissions and roles after identity is established.
```

```quiz score
Q: 5.015. A legacy JDBC account has been created. What is still needed before it can manage a specific VM?
- Make the VM the engine's SPM
* Import or select the user in OLVM and assign the appropriate permission on the resource
- Assume account creation automatically gives SuperUser access
- Add the user's password to the NFS export
> Lab 7 distinguishes identity creation from OLVM authorization. A valid account alone does not grant the required resource operations.
```

```quiz score
Q: 5.016. In the recorded course, what command shows details of a specific user?
* ovirt-aaa-jdbc-tool user show <username>
- ovirt-aaa-jdbc-tool query user
- ovirt-aaa-jdbc-tool user list
- ovirt-aaa-jdbc-tool display user
> `ovirt-aaa-jdbc-tool user show <username>` displays details for a specific user. It is used when you need to inspect one account rather than query broad lists.
```

```quiz score
Q: 5.017. In the recorded course, what command is used to edit user attributes?
- ovirt-aaa-jdbc-tool update user
- ovirt-aaa-jdbc-tool user modify
- ovirt-aaa-jdbc-tool user change
* ovirt-aaa-jdbc-tool user edit
> `ovirt-aaa-jdbc-tool user edit` modifies user attributes. The edit action changes existing account properties rather than creating or deleting the user.
```

```quiz score
Q: 5.018. Why define a role with only the operations a user needs?
- To grant access automatically to every data center
- To replace authentication entirely
* To limit privileges while allowing the intended work
- To allow unlimited resource use regardless of quota
> Roles describe allowed operations; permission assignments connect a principal and role to a resource scope. Apply only the access needed.
```

```quiz score
Q: 5.019. What is the effect of disabling an account in the applicable identity system?
- It removes all group roles from the entire environment
- It converts the user into a host service identity
- It automatically deletes every VM the user can view
* It prevents that account from authenticating while its resource permissions are a separate concern
> The account lesson distinguishes user state from authorization. Use the management procedure appropriate to the configured identity provider.
```

```quiz score
Q: 5.020. A user can manage one VM but not an unrelated VM. What should be checked?
- Whether the engine's DNS name is longer than both VM names
- Whether the user belongs to the host's Linux KVM group
- Whether both VMs use the same wallpaper
* Whether the role is assigned at the intended resource scope
> A role assignment includes scope. Permission on one object does not necessarily grant operations on unrelated objects.
```

## Task 3: Manage Groups

```quiz score
Q: 5.021. In the recorded course, what command is used to add a new group?
- groupadd
- engine-group-create
- ovirt-aaa-jdbc-tool add-group
* ovirt-aaa-jdbc-tool group add
> `ovirt-aaa-jdbc-tool group add` creates a new group in the internal domain. Linux `groupadd` would affect operating system groups, not OLVM internal groups.
```

```quiz score
Q: 5.022. In the recorded course, what command is used to add users to a group?
- ovirt-aaa-jdbc-tool group adduser
- ovirt-aaa-jdbc-tool user-to-group
* ovirt-aaa-jdbc-tool group-manage useradd
- usermod -aG
> `ovirt-aaa-jdbc-tool group-manage useradd` adds users to an OLVM internal group. Group membership is managed through the `group-manage` subcommand.
```

```quiz score
Q: 5.023. What is a practical benefit of assigning a role to a group instead of repeating it for individual users?
- Every member bypasses authentication
- Every member automatically becomes SuperUser
- The group becomes a storage domain
* Group membership can be managed while the shared permission assignment remains centralized
> Group-based permissions simplify consistent access management. Membership and permission scope still need review.
```

```quiz score
Q: 5.024. In the recorded course, what command shows details of a specific group?
* ovirt-aaa-jdbc-tool group show <groupname>
- ovirt-aaa-jdbc-tool group display
- ovirt-aaa-jdbc-tool query group
- ovirt-aaa-jdbc-tool group list
> `ovirt-aaa-jdbc-tool group show <groupname>` displays details for a specific group. It is the group equivalent of showing a specific user.
```

```quiz score
Q: 5.025. What are nested groups?
- Groups spanning multiple data centers
- Encrypted group structures
* Hierarchical structures where groups can contain other groups (sub-groups) as members
- Groups within VMs
> Nested groups are hierarchical groups that can contain other groups as members. They allow permissions or membership structure to mirror organizational hierarchy.
```

```quiz score
Q: 5.026. How do you add a subgroup to a parent group?
* ovirt-aaa-jdbc-tool group-manage groupadd <parent-group> --group=<sub-group>
- ovirt-aaa-jdbc-tool group nest
- ovirt-aaa-jdbc-tool subgroup create
- ovirt-aaa-jdbc-tool group add-subgroup
> `ovirt-aaa-jdbc-tool group-manage groupadd <parent-group> --group=<sub-group>` adds a subgroup to a parent group. This creates nested membership rather than adding an individual user.
```

```quiz score
Q: 5.027. What is a benefit of nested groups?
* Reduced redundancy in permission assignments
- Faster VM performance
- Better network performance
- Lower storage costs
> Nested groups reduce redundant permission assignments because permissions can be granted to a parent group and inherited through group membership. This simplifies administration in larger environments.
```

```quiz score
Q: 5.028. In the recorded course, what command is used to query user or group information?
* ovirt-aaa-jdbc-tool query
- ovirt-aaa-jdbc-tool search
- ovirt-aaa-jdbc-tool list
- ovirt-aaa-jdbc-tool find
> `ovirt-aaa-jdbc-tool query` searches user or group information. Querying is used when you need filtered lists rather than details for one known object.
```

```quiz score
Q: 5.029. What parameter specifies what to query (users or groups)?
- --target
* --what
- --type
- --object
> The `--what` parameter specifies the object type to query, such as user or group. It tells the tool which category of records to search.
```

```quiz score
Q: 5.030. What parameter specifies the search pattern when querying?
- --filter
- --search
* --pattern
- --match
> The `--pattern` parameter specifies the search pattern. This lets administrators filter query results instead of returning every matching object type blindly.
```

```quiz score
Q: 5.031. A user joins an OLVM group but still lacks the expected VM access. What should be verified?
- Only whether the group name contains the word admin
- Only whether the guest has a larger disk
* The group's effective permission assignment and its applicable resource scope
- Only whether the user can read the training transcript
> Membership is useful only when the group has the relevant permissions. Verify both identity membership and authorization.
```

## Task 4: Manage Account Settings

```quiz score
Q: 5.032. In the recorded course, what command is used to view current account settings?
- ovirt-aaa-jdbc-tool settings list
- ovirt-aaa-jdbc-tool display-settings
* ovirt-aaa-jdbc-tool settings show
- ovirt-aaa-jdbc-tool show settings
> `ovirt-aaa-jdbc-tool settings show` displays current account settings. These settings include login and lockout-related values for the internal authentication store.
```

```quiz score
Q: 5.033. In the recorded course, what command is used to modify account settings?
- ovirt-aaa-jdbc-tool settings modify
* ovirt-aaa-jdbc-tool settings set
- ovirt-aaa-jdbc-tool settings change
- ovirt-aaa-jdbc-tool set settings
> `ovirt-aaa-jdbc-tool settings set` modifies account settings. It changes the configured policy value rather than simply displaying it.
```

```quiz score
Q: 5.034. What setting controls the maximum number of failed login attempts before account lockout?
- FAILED_LOGIN_LIMIT
- MAX_FAILURES
- LOGIN_ATTEMPTS_MAX
* MAX_FAILURE_SINCE_SUCCESS
> `MAX_FAILURE_SINCE_SUCCESS` controls how many failed login attempts can occur since the last successful login before lockout behavior applies. It is the account lockout threshold setting tested here.
```

```quiz score
Q: 5.035. A directory user is locked out after failed logins. Why is assigning an OLVM role insufficient to fix the login?
- A role always changes the user's password immediately
* Account lockout is an authentication concern, separate from resource permissions
- A VM snapshot automatically unlocks every account
- The SPM owns all directory lockout settings
> The account settings section discusses lockout controls. Resolve the condition in the applicable identity system, then separately verify permissions.
```

## Task 5: Manage Quotas

```quiz score
Q: 5.036. What is a quota in OLVM?
- A network bandwidth limit
- A user count limit
- A backup limit
* A resource limitation tool to restrict memory, CPU, and storage usage
> A quota is a resource limitation tool for controlling memory, CPU, and storage usage. Quotas help administrators prevent one project or group from consuming excessive shared resources.
```

```quiz score
Q: 5.037. Which three types of resources can quotas limit? Select the option listing the complete correct set.
- Memory; Email storage; Storage
* Memory; CPU; Storage
- Memory; CPU; Email storage
- Email storage; CPU; Storage
> OLVM quotas can limit memory, CPU, and storage. Email storage and network ports are outside the quota categories tested in this OLVM context.
```

```quiz score
Q: 5.038. What are the three quota modes in OLVM? Select the option listing the complete correct set.
* Enforced; Audit; Disabled
- Enforced; Monitored; Disabled
- Monitored; Audit; Disabled
- Enforced; Audit; Monitored
> The three quota modes are Enforced, Audit, and Disabled. These modes control whether quota limits are actively enforced, only logged, or turned off.
```

```quiz score
Q: 5.039. What does the "Enforced" quota mode do?
- Logs violations only
- Disables quotas
* Puts into effect the quota limits that have been set
- Sends email alerts
> Enforced mode applies the configured quota limits. In this mode, actions that exceed quota can be blocked rather than merely recorded.
```

```quiz score
Q: 5.040. What does the "Audit" quota mode do?
- Disables all quotas
* Logs quota violations without blocking users
- Blocks all users
- Enforces strict limits
> Audit mode logs quota violations but does not block users from exceeding limits. This is useful for observing resource use before turning on strict enforcement.
```

```quiz score
Q: 5.041. What does the "Disabled" quota mode do?
- Enables strict quotas
- Sends warnings only
* Turns off runtime and storage limitations defined by quotas
- Logs violations
> Disabled mode turns off the runtime and storage limitations defined by quotas. Quota definitions may exist, but they are not actively limiting resource use.
```

```quiz score
Q: 5.042. At what level is the quota mode configured?
* Data Center level
- Host level
- VM level
- Cluster level
> Quota mode is configured at the Data Center level. This makes quotas part of the Data Center resource-management policy rather than a single host or VM setting.
```

```quiz score
Q: 5.043. Which quota resource mapping is correct?
- Storage quota determines the host's CPU architecture
- Cluster quota limits only storage and never CPU or memory
* Cluster quota concerns CPU and memory, while storage quota concerns storage consumption
- Account lockout thresholds are storage quota limits
> The quota lesson separates cluster CPU and memory resources from storage resources. One later transcript sentence mixes these terms; this question uses the earlier explicit distinction.
```

```quiz score
Q: 5.044. What does quota grace represent in the course's resource-limit discussion?
- An unlimited exemption from all quota checks
* Additional resource allowance beyond the threshold
- A replacement for user authentication
- A guaranteed number of minutes during which passwords never expire
> Quota grace concerns an additional resource allowance. It is not a time-based account grace period.
```

```quiz score
Q: 5.045. Where in the Administration Portal do you create and manage quota policies?
* Administration -> Quota
- Configuration -> Quotas
- Storage -> Quotas
- Compute -> Quotas
> Quota policies are created and managed from Administration -> Quota in the Administration Portal. Quotas are administrative resource-governance objects rather than VM or storage-only objects.
```

## Acknowledgements

- **Author** - OLVM training team
- **Sources** - User-supplied training transcripts, section quiz-bank files, and Lab 7 OLVM content
- **Last Updated** - October 2026
