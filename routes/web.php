use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/github/redirect', function () {
    return Socialite::driver('github')->redirect();
});

Route::get('/test-user', function () {
    $user = User::find(6); // Encuentra el usuario por su ID
    if ($user) {
        $user->delete(); // Elimina el usuario
        return 'User deleted';
    }
    return 'User not found';
});

Route::get('/github-auth/callback', function () {
    $user_github = Socialite::driver('github')->stateless()->user();

    $user = User::updateOrCreate([
        'email' => $user_github->getEmail(),
    ], [
        'name' => $user_github->getName() ?? explode('@', $user_github->getEmail())[0],
        'email' => $user_github->getEmail(),
    ]);

    Auth::login($user, true);

    return redirect('/dashboard');
});

Route::get('/google-auth/redirect', function () {
    return Socialite::driver('google')->redirect();
});
 
Route::get('/google-auth/callback', function () {
    $user_google = Socialite::driver('google')->stateless()->user();

    $user = User::updateOrCreate([
        'email' => $user_google->email,
    ], [
        'name' => $user_google->name,
        'email' => $user_google->email,
    ]);

    Auth::login($user, true);

    return redirect('/dashboard');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
