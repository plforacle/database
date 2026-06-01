# Section 7: User Management
## Introduction

This lab contains the Section 7 practice questions for **User Management** from the OLVM Exam 1Z0-1170 quiz bank.

Estimated Time: 60 minutes

```quiz-config
passing: 75
badge: images/badge.png
```

### Objectives

In this lab, you will review the OLVM concepts covered by this exam objective and check your understanding with practice questions.

## Practice Questions

```quiz

Q: 1. Which administrative task involves creating and managing user accounts with appropriate roles?
- A. Monitoring resources
* B. Managing user setup and setting permission levels
- C. Generating reports
- D. Managing physical resources
> User setup and permission levels determine who can access OLVM resources and what actions they can perform. This is separate from monitoring or reporting because it controls identity and authorization.

Q: 2. What are the main categories of resources that administrators manage in OLVM? (Choose 2)
* A. Physical servers
- B. Email systems
* C. Virtual resources (VMs, vCPUs, memory, storage, networks)
- D. Office applications
> OLVM administrators manage both physical servers and virtual resources such as VMs, vCPUs, memory, storage, and networks. The platform spans the physical host layer and the virtual infrastructure layer.

Q: 3. What should administrators set up for critical resource thresholds?
- A. Firewalls
* B. Alerts for critical thresholds and anomalies
- C. User accounts
- D. Virtual machines
> Administrators should configure alerts for critical thresholds and anomalies so resource or health issues are noticed early. Alerts support proactive operations rather than waiting for users to report failures.

Q: 4. What can administrators use to organize and manage OLVM objects?
- A. Folders
* B. Custom tags
- C. Directories
- D. Scripts
> Custom tags help organize and manage OLVM objects logically. Tags make it easier to group, search, and categorize resources beyond the built-in hierarchy.

Q: 5. What are the two types of user domains in OLVM? (Choose 2)
* A. Local domain
- B. Cloud domain
* C. External domain
- D. Hybrid domain
> OLVM supports local domains and external domains. Local domains are internal to OLVM, while external domains connect to directory services such as Active Directory or LDAP.

Q: 6. What is the name of the default local domain created during initial OLVM setup?
- A. local
- B. default
* C. internal
- D. admin
> The default local domain is named `internal`. This domain exists after initial setup and contains the built-in administrative user.

Q: 7. What is the default user created in the internal domain?
- A. root
- B. administrator
* C. admin
- D. superuser
> The default user in the internal domain is `admin`. This is the initial administrative account used to log in after engine setup.

Q: 8. When is the password for the default admin user set?
- A. After first login
* B. During engine setup
- C. Never, it's automatically generated
- D. When first VM is created
> The password for the default `admin` user is set during engine setup. That setup process creates the initial administrative access path into the OLVM Administration Portal.

Q: 9. What tool is used to create additional users on the internal domain?
- A. useradd
- B. ovirt-user-tool
* C. ovirt-aaa-jdbc-tool
- D. engine-user-create
> `ovirt-aaa-jdbc-tool` manages users and groups in the internal domain. Standard Linux `useradd` would create operating system users, not OLVM internal authentication users.

Q: 10. What are users created on local domains called?
- A. Internal users
* B. Local users
- C. Domain users
- D. System users
> Users created in local OLVM domains are called local users. They are stored and managed inside OLVM's internal authentication store rather than an external directory.

Q: 11. Which two external directory servers can be attached to OLVM? (Choose 2)
* A. Active Directory
- B. NIS
* C. OpenLDAP
- D. Kerberos
> OLVM can attach to Active Directory and OpenLDAP as external directory services. These integrations let organizations use existing enterprise identity sources.

Q: 12. What are users created on external domains called?
- A. External users
* B. Directory users
- C. LDAP users
- D. Remote users
> Users from external domains are called directory users because their identities are stored and authenticated by the external directory service. OLVM consumes those identities rather than storing the password locally.

Q: 13. What are the benefits of using external domains? (Choose 3)
* A. Centralized user management
- B. Lower costs
* C. Single sign-on capabilities
* D. Adherence to organization's security policies
- E. Faster VM performance
> External domains provide centralized user management, single sign-on capabilities, and alignment with organizational security policies. They do not directly improve VM performance or reduce infrastructure cost by themselves.

Q: 14. Where do directory users authenticate?
- A. Within OLVM's internal system
* B. Against their respective external directory servers
- C. At the hypervisor level
- D. Through the database
> Directory users authenticate against their external directory servers. OLVM delegates authentication to that source and then applies OLVM permissions and roles after identity is established.

Q: 15. What command is used to add a new user to the internal domain?
- A. ovirt-aaa-jdbc-tool add-user
* B. ovirt-aaa-jdbc-tool user add
- C. useradd
- D. engine-user-add
> `ovirt-aaa-jdbc-tool user add` adds a new user to the internal domain. The command structure uses the object type first, followed by the action.

Q: 16. What parameter is used to set a user's password when creating a user?
- A. --pass
- B. --pwd
* C. --password
- D. --passwd
> The `--password` parameter sets the user's password during user creation. Using the exact parameter matters because the tool expects its documented option names.

Q: 17. What command shows details of a specific user?
- A. ovirt-aaa-jdbc-tool user list
* B. ovirt-aaa-jdbc-tool user show <username>
- C. ovirt-aaa-jdbc-tool display user
- D. ovirt-aaa-jdbc-tool query user
> `ovirt-aaa-jdbc-tool user show <username>` displays details for a specific user. It is used when you need to inspect one account rather than query broad lists.

Q: 18. What command is used to edit user attributes?
- A. ovirt-aaa-jdbc-tool user modify
- B. ovirt-aaa-jdbc-tool user change
* C. ovirt-aaa-jdbc-tool user edit
- D. ovirt-aaa-jdbc-tool update user
> `ovirt-aaa-jdbc-tool user edit` modifies user attributes. The edit action changes existing account properties rather than creating or deleting the user.

Q: 19. Which parameter is used to specify the attribute to change when editing a user?
- A. --parameter
- B. --field
* C. --attribute
- D. --property
> The `--attribute` parameter identifies the user attribute to change during an edit operation. This keeps the command explicit about which account property is being modified.

Q: 20. How do you disable a user account?
- A. ovirt-aaa-jdbc-tool user delete
- B. ovirt-aaa-jdbc-tool user disable
* C. ovirt-aaa-jdbc-tool user edit --flag=+disabled
- D. ovirt-aaa-jdbc-tool user lock
> Disabling a user is done by editing the user and adding the disabled flag. This preserves the account object while preventing it from being used.

Q: 21. How do you re-enable a disabled user account?
- A. ovirt-aaa-jdbc-tool user enable
* B. ovirt-aaa-jdbc-tool user edit --flag=+enabled
- C. ovirt-aaa-jdbc-tool user unlock
- D. ovirt-aaa-jdbc-tool user activate
> Re-enabling a user reverses the disabled state by editing the user and setting the enabled flag. The account remains the same object with access restored.

Q: 22. What command is used to add a new group?
- A. ovirt-aaa-jdbc-tool add-group
- B. groupadd
* C. ovirt-aaa-jdbc-tool group add
- D. engine-group-create
> `ovirt-aaa-jdbc-tool group add` creates a new group in the internal domain. Linux `groupadd` would affect operating system groups, not OLVM internal groups.

Q: 23. What command is used to add users to a group?
- A. ovirt-aaa-jdbc-tool group adduser
* B. ovirt-aaa-jdbc-tool group-manage useradd
- C. ovirt-aaa-jdbc-tool user-to-group
- D. usermod -aG
> `ovirt-aaa-jdbc-tool group-manage useradd` adds users to an OLVM internal group. Group membership is managed through the `group-manage` subcommand.

Q: 24. When specifying multiple users to add to a group, how can they be separated? (Choose 2)
* A. With commas
- B. With semicolons
* C. With multiple --user flags separated by spaces
- D. With pipe symbols
> Multiple users can be specified with comma-separated values or with repeated `--user` flags. Both forms let administrators add more than one user in one group-management operation.

Q: 25. What command lists all group manage options?
- A. ovirt-aaa-jdbc-tool group --help
* B. ovirt-aaa-jdbc-tool group-manage --help
- C. ovirt-aaa-jdbc-tool help groups
- D. man ovirt-groups
> `ovirt-aaa-jdbc-tool group-manage --help` lists group-management options. It is the relevant help command for membership operations such as adding users or groups.

Q: 26. What command shows details of a specific group?
- A. ovirt-aaa-jdbc-tool group list
- B. ovirt-aaa-jdbc-tool group display
* C. ovirt-aaa-jdbc-tool group show <groupname>
- D. ovirt-aaa-jdbc-tool query group
> `ovirt-aaa-jdbc-tool group show <groupname>` displays details for a specific group. It is the group equivalent of showing a specific user.

Q: 27. What are nested groups?
- A. Groups within VMs
* B. Hierarchical structures where groups can contain other groups (sub-groups) as members
- C. Encrypted group structures
- D. Groups spanning multiple data centers
> Nested groups are hierarchical groups that can contain other groups as members. They allow permissions or membership structure to mirror organizational hierarchy.

Q: 28. How do you add a subgroup to a parent group?
- A. ovirt-aaa-jdbc-tool group add-subgroup
- B. ovirt-aaa-jdbc-tool group nest
* C. ovirt-aaa-jdbc-tool group-manage groupadd <parent-group> --group=<sub-group>
- D. ovirt-aaa-jdbc-tool subgroup create
> `ovirt-aaa-jdbc-tool group-manage groupadd <parent-group> --group=<sub-group>` adds a subgroup to a parent group. This creates nested membership rather than adding an individual user.

Q: 29. What is a benefit of nested groups?
- A. Faster VM performance
* B. Reduced redundancy in permission assignments
- C. Lower storage costs
- D. Better network performance
> Nested groups reduce redundant permission assignments because permissions can be granted to a parent group and inherited through group membership. This simplifies administration in larger environments.

Q: 30. What command is used to query user or group information?
- A. ovirt-aaa-jdbc-tool search
- B. ovirt-aaa-jdbc-tool find
* C. ovirt-aaa-jdbc-tool query
- D. ovirt-aaa-jdbc-tool list
> `ovirt-aaa-jdbc-tool query` searches user or group information. Querying is used when you need filtered lists rather than details for one known object.

Q: 31. What parameter specifies what to query (users or groups)?
- A. --type
* B. --what
- C. --object
- D. --target
> The `--what` parameter specifies the object type to query, such as user or group. It tells the tool which category of records to search.

Q: 32. What parameter specifies the search pattern when querying?
- A. --search
- B. --filter
* C. --pattern
- D. --match
> The `--pattern` parameter specifies the search pattern. This lets administrators filter query results instead of returning every matching object type blindly.

Q: 33. To query all users, what value is used with --what parameter?
- A. users
* B. user
- C. all-users
- D. userlist
> To query users, the `--what` value is `user`. The tool expects the singular object type value in this command syntax.

Q: 34. To query all groups, what value is used with --what parameter?
- A. groups
* B. group
- C. all-groups
- D. grouplist
> To query groups, the `--what` value is `group`. Like user queries, the command uses the singular object type value.

Q: 35. What command is used to view current account settings?
- A. ovirt-aaa-jdbc-tool settings list
* B. ovirt-aaa-jdbc-tool settings show
- C. ovirt-aaa-jdbc-tool show settings
- D. ovirt-aaa-jdbc-tool display-settings
> `ovirt-aaa-jdbc-tool settings show` displays current account settings. These settings include login and lockout-related values for the internal authentication store.

Q: 36. What command is used to modify account settings?
- A. ovirt-aaa-jdbc-tool settings change
- B. ovirt-aaa-jdbc-tool settings modify
* C. ovirt-aaa-jdbc-tool settings set
- D. ovirt-aaa-jdbc-tool set settings
> `ovirt-aaa-jdbc-tool settings set` modifies account settings. It changes the configured policy value rather than simply displaying it.

Q: 37. What is the default value for MAX_LOGIN_MINUTES?
- A. 60 minutes
- B. 1,440 minutes
* C. 10,080 minutes
- D. Unlimited
> `MAX_LOGIN_MINUTES` defaults to 10,080 minutes, which equals seven days. This setting controls the login/session lifetime value used by the internal account system.

Q: 38. What setting controls the maximum number of failed login attempts before account lockout?
- A. MAX_FAILURES
* B. MAX_FAILURE_SINCE_SUCCESS
- C. LOGIN_ATTEMPTS_MAX
- D. FAILED_LOGIN_LIMIT
> `MAX_FAILURE_SINCE_SUCCESS` controls how many failed login attempts can occur since the last successful login before lockout behavior applies. It is the account lockout threshold setting tested here.

Q: 39. What is the default value for MAX_FAILURE_SINCE_SUCCESS?
- A. 3
* B. 5
- C. 10
- D. Unlimited
> The default `MAX_FAILURE_SINCE_SUCCESS` value is 5. After that number of failed attempts since the last success, the account can be locked according to policy.

Q: 40. What commands lock and unlock user accounts? (Choose 2)
* A. ovirt-aaa-jdbc-tool user lock <username>
* B. ovirt-aaa-jdbc-tool user unlock <username>
- C. ovirt-aaa-jdbc-tool user disable
- D. ovirt-aaa-jdbc-tool user freeze
> `ovirt-aaa-jdbc-tool user lock <username>` and `user unlock <username>` lock and unlock accounts. Locking is different from deleting or permanently disabling a user.

Q: 41. What is a quota in OLVM?
- A. A backup limit
* B. A resource limitation tool to restrict memory, CPU, and storage usage
- C. A user count limit
- D. A network bandwidth limit
> A quota is a resource limitation tool for controlling memory, CPU, and storage usage. Quotas help administrators prevent one project or group from consuming excessive shared resources.

Q: 42. Which three types of resources can quotas limit? (Choose 3)
* A. Memory
- B. Email storage
* C. CPU
* D. Storage
- E. Network ports
> OLVM quotas can limit memory, CPU, and storage. Email storage and network ports are outside the quota categories tested in this OLVM context.

Q: 43. What are the three quota modes in OLVM? (Choose 3)
* A. Enforced
- B. Monitored
* C. Audit
* D. Disabled
- E. Warning
> The three quota modes are Enforced, Audit, and Disabled. These modes control whether quota limits are actively enforced, only logged, or turned off.

Q: 44. What does the "Enforced" quota mode do?
- A. Logs violations only
* B. Puts into effect the quota limits that have been set
- C. Disables quotas
- D. Sends email alerts
> Enforced mode applies the configured quota limits. In this mode, actions that exceed quota can be blocked rather than merely recorded.

Q: 45. What does the "Audit" quota mode do?
- A. Blocks all users
* B. Logs quota violations without blocking users
- C. Enforces strict limits
- D. Disables all quotas
> Audit mode logs quota violations but does not block users from exceeding limits. This is useful for observing resource use before turning on strict enforcement.

Q: 46. What does the "Disabled" quota mode do?
- A. Enables strict quotas
- B. Logs violations
* C. Turns off runtime and storage limitations defined by quotas
- D. Sends warnings only
> Disabled mode turns off the runtime and storage limitations defined by quotas. Quota definitions may exist, but they are not actively limiting resource use.

Q: 47. At what level is the quota mode configured?
- A. Cluster level
* B. Data Center level
- C. Host level
- D. VM level
> Quota mode is configured at the Data Center level. This makes quotas part of the Data Center resource-management policy rather than a single host or VM setting.

Q: 48. What is a cluster threshold in quota settings?
- A. CPU speed limit
* B. The limit on total storage that can be allocated across all VMs within a specific cluster
- C. Network bandwidth limit
- D. Number of VMs allowed
> A cluster threshold is the total storage limit that can be allocated across all VMs within a specific cluster. It defines the planned resource boundary for that cluster in the quota policy.

Q: 49. What is cluster grace in quota settings?
- A. Extra time before enforcement
* B. The amount of cluster resources available after exhausting the cluster threshold
- C. Backup storage allocation
- D. Emergency CPU allocation
> Cluster grace is the amount of cluster resources available after the threshold is exhausted. It provides a limited buffer beyond the nominal threshold before resource use is fully constrained.

Q: 50. Where in the Administration Portal do you create and manage quota policies?
- A. Compute -> Quotas
- B. Storage -> Quotas
* C. Administration -> Quota
- D. Configuration -> Quotas
> Quota policies are created and managed from Administration -> Quota in the Administration Portal. Quotas are administrative resource-governance objects rather than VM or storage-only objects.

```

## Acknowledgements

- **Author** - Perside Foster
- **Source Material** - OLVM bootcamp training and Oracle Linux Virtualization Manager documentation
