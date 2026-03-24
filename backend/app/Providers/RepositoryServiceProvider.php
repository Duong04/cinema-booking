<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\User\UserRepositoryInterface;
use App\Repositories\User\UserRepository;
use App\Repositories\Role\RoleRepository;
use App\Repositories\Role\RoleRepositoryInterface;
use App\Repositories\Action\ActionRepository;
use App\Repositories\Action\ActionRepositoryInterface;
use App\Repositories\Permission\PermissionRepository;
use App\Repositories\Permission\PermissionRepositoryInterface;
use App\Repositories\PermissionAction\PermissionActionRepository;
use App\Repositories\PermissionAction\PermissionActionRepositoryInterface;
use App\Repositories\RolePermission\RolePermissionRepository;
use App\Repositories\RolePermission\RolePermissionRepositoryInterface;
use App\Repositories\City\CityRepository;
use App\Repositories\City\CityRepositoryInterface;
use App\Repositories\Cinema\CinemaRepository;
use App\Repositories\Cinema\CinemaRepositoryInterface;
use App\Repositories\CinemaChain\CinemaChainRepository;
use App\Repositories\CinemaChain\CinemaChainRepositoryInterface;
use App\Repositories\Room\RoomRepository;
use App\Repositories\Room\RoomRepositoryInterface;
use App\Repositories\SeatType\SeatTypeRepository;
use App\Repositories\SeatType\SeatTypeRepositoryInterface;
use App\Repositories\Seat\SeatRepository;
use App\Repositories\Seat\SeatRepositoryInterface;
use App\Repositories\Movie\MovieRepository;
use App\Repositories\Movie\MovieRepositoryInterface;
use App\Repositories\Genre\GenreRepository;
use App\Repositories\Genre\GenreRepositoryInterface;
use App\Repositories\MovieGenre\MovieGenreRepository;
use App\Repositories\MovieGenre\MovieGenreRepositoryInterface;
use App\Repositories\Showtime\ShowtimeRepository;
use App\Repositories\Showtime\ShowtimeRepositoryInterface;
use App\Repositories\ShowtimePrice\ShowtimePriceRepository;
use App\Repositories\ShowtimePrice\ShowtimePriceRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(ActionRepositoryInterface::class, ActionRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(PermissionActionRepositoryInterface::class, PermissionActionRepository::class);
        $this->app->bind(RolePermissionRepositoryInterface::class, RolePermissionRepository::class);
        $this->app->bind(CityRepositoryInterface::class, CityRepository::class);
        $this->app->bind(CinemaRepositoryInterface::class, CinemaRepository::class);
        $this->app->bind(CinemaChainRepositoryInterface::class, CinemaChainRepository::class);
        $this->app->bind(RoomRepositoryInterface::class, RoomRepository::class);
        $this->app->bind(SeatTypeRepositoryInterface::class, SeatTypeRepository::class);
        $this->app->bind(SeatRepositoryInterface::class, SeatRepository::class);
        $this->app->bind(MovieRepositoryInterface::class, MovieRepository::class);
        $this->app->bind(GenreRepositoryInterface::class, GenreRepository::class);
        $this->app->bind(MovieGenreRepositoryInterface::class, MovieGenreRepository::class);
        $this->app->bind(ShowtimeRepositoryInterface::class, ShowtimeRepository::class);
        $this->app->bind(ShowtimePriceRepositoryInterface::class, ShowtimePriceRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
