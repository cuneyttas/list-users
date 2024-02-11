function App() {

    const { useState } = React
    const [users, setUsers] = useState([]);
    const [isLoading, setLoading] = useState(true);

    const [userInfo, setUserInfo] = useState(null);
    const [userIsLoading, setuserLoading] = useState(false);

    // Sayfa açıldığında User'ın listesini state'e ekleme
    fetch(`${apiUrl}ulapi/v1/users`)
        .then(response => response.json())
        .then(response => setUsers(response))
        .catch(error => console.error('Error fetching users:', error))
        .finally(() => {
            setLoading(false);
        });

    // User ismine tıklanıldığında Yapılacaklar
    const getUserInfo = (userID) => {
        // Loading'i aktif et
        setuserLoading(true);

        // fetch fonskyonu sonucunda userInfo state'i güncellenecek
        fetch(`${apiUrl}ulapi/v1/users/${userID}`)
            .then(response => response.json())
            .then(response => setUserInfo(response))
            .catch(error => console.error('Error fetching users:', error))
            .finally(() => {
                setuserLoading(false);
            });
    };

    return (
        <>

            <h1 class="wp-heading-inline">Pages</h1>
            <table class="wp-list-table widefat fixed striped table-view-list pages">
                <thead>
                    <tr>
                        <td scope="col" id="author" class="manage-column check-column">ID</td>
                        <th scope="col" id="author" class="manage-column">Name</th>
                        <th scope="col" id="author" class="manage-column column-author">Username</th>
                    </tr>
                </thead>

                <tbody id="the-list">
                    <UsersList users={users} isLoading={isLoading} handleClick={getUserInfo} />
                </tbody>

                <tfoot>
                    <tr>
                        <td scope="col" id="author" class="manage-column check-column">ID</td>
                        <th scope="col" id="author" class="manage-column">Name</th>
                        <th scope="col" id="author" class="manage-column column-author">Username</th>
                    </tr>
                </tfoot>

            </table>

            <UserInfo user={userInfo} isLoading={userIsLoading} />

        </>
    );
}

const root = ReactDOM.createRoot(document.getElementById('users-app'));
root.render(<App />)

function UsersList({ users, isLoading, handleClick }) {

    if (isLoading) {
        return (
            <tr>
                <td colspan="3">Loading...</td>
            </tr>
        );
    }


    if (!users.success || !users.data) {
        return (
            <tr>
                <td colspan="3">User list could not be received: {users.error}</td>
            </tr>
        );
    }

    return (
        <>

            {users.data.map((user) =>
                <tr key={user.id} className="iedit author-self level-0 type-page status-publish hentry">
                    <th className="check-column">{user.id}</th>
                    <td className="title column-title has-row-actions column-primary page-title">
                        <strong><a className="row-title" onClick={() => handleClick(user.id)}>{user.name}</a></strong>
                    </td>
                    <td className="author column-author" data-colname="Author">{user.username}</td>
                </tr>
            )}

        </>
    );
}

function UserInfo({ user, isLoading }) {

    if (isLoading) {
        return (
            <p>Loading...</p>
        );
    }

    if (!user) {
        return (
            <p>Click one of the users above!</p>
        );
    }

    if (!user.success || !user.data) {
        return (
            <p>User list could not be received: {user.error}</p>
        );
    }

    return (
        <div id="user-data">

            <h2>User Information</h2>
            <p><b>ID:</b> {user.data.id}</p>
            <p><b>Name:</b> {user.data.name}</p>
            <p><b>Username:</b> {user.data.username}</p>
            <p><b>Email:</b> {user.data.email}</p>
            <p><b>Phone:</b> {user.data.phone}</p>
            <p><b>Website:</b> {user.data.website}</p>

            <h4><u>Address</u></h4>
            <p><b>Street:</b> {user.data.address.street}</p>
            <p><b>Suite:</b> {user.data.address.suite}</p>
            <p><b>City:</b> {user.data.address.city}</p>
            <p><b>Zipcode:</b> {user.data.address.zipcode}</p>
            <p><b>Geo(Lat/Lng):</b> {user.data.address.geo.lat}/{user.data.address.geo.lng}</p>

            <h4><u>Company</u></h4>
            <p><b>Name:</b> {user.data.company.name}</p>
            <p><b>Catch Phrase:</b> {user.data.company.catchPhrase}</p>
            <p><b>BS:</b> {user.data.company.bs}</p>

        </div>
    );
}