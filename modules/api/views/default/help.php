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
        ?description    <User Profile Description>
        ?password       <Preferred Password>


   [Stories]

    /api/story/write
    /api/story/<id>
