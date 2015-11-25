Welcome to Take365 API!
-----------------------

Available methods are:

   [Auth]

    /api/auth/login
        ?username       <UserName>
        ?password       <User Password>

    /api/auth/logout


   [Users]

    /api/user/chek-username
        ?username       <Username>

    /api/user/check-email
        ?email          <Preferred Email>

    /api/user/register
        ?username       <Preferred Username>
        ?email          <Preferred Email>
        ?password       <Preferred Password>

    /api/user/update-profile
        ?id             <User Id>
        ?username       <Preferred Username>
        ?password       <Preferred Password>
        ?email          <Preferred Email>
        ?description    <User Profile Description>


   [Stories]

    /api/story/<id>

    /api/story/write
        ?id             <Story Id>                  (If not given, a new story will be created)
        ?title          <Story Title>
        ?Description    <Story Description>


    [Media]

    /api/media/upload
        ?targetId       <Target Object Id>
        ?targetType     <Target Object Type>        (1 for user, 2 for story)
        ?mediaType      <Type of Uploaded media>    (eg. "userpic", "storyImage")
