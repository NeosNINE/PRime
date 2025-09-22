<div class="user-nav">
    <ul>
        <li><a href="{{ route('user.profile') }}">Profile</a></li>
        <li>
            <a href="#" class="link-logout">Logout</a>
            <form action="{{ route('logout') }}" method="POST" class="form-logout">
                @csrf
            </form>
        </li>
    </ul>
</div>
<hr>
