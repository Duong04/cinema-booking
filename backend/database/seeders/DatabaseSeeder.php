<?php

namespace Database\Seeders;

use App\Models\Action;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Cinema;
use App\Models\CinemaChain;
use App\Models\City;
use App\Models\Combo;
use App\Models\Genre;
use App\Models\Membership;
use App\Models\Movie;
use App\Models\Payment;
use App\Models\Permission;
use App\Models\Promotion;
use App\Models\Role;
use App\Models\Room;
use App\Models\Seat;
use App\Models\SeatHold;
use App\Models\SeatType;
use App\Models\Showtime;
use App\Models\ShowtimePrice;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    private array $roles = [];

    private array $permissions = [];

    private array $actions = [];

    private array $users = [];

    private array $cities = [];

    private array $chains = [];

    private array $cinemas = [];

    private array $rooms = [];

    private array $seatTypes = [];

    private array $seatsByRoom = [];

    private array $genres = [];

    private array $movies = [];

    private array $showtimes = [];

    private array $combosByCinema = [];

    private array $promotions = [];

    public function run(): void
    {
        $this->resetDatabase();

        $this->seedAccessControl();
        $this->seedUsers();
        $this->seedLocations();
        $this->seedRoomsAndSeats();
        $this->seedMovies();
        $this->seedMovieReviews();
        $this->seedShowtimes();
        $this->seedCombos();
        $this->seedPromotions();
        $this->seedBookings();
        $this->seedActivityLogs();
        $this->seedLoginData();
    }

    private function resetDatabase(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach ([
            'sessions',
            'password_reset_tokens',
            'activity_logs',
            'memberships',
            'promotion_usages',
            'promotions',
            'booking_combos',
            'combos',
            'payment_attempts',
            'payments',
            'seat_holds',
            'booking_status_logs',
            'booking_items',
            'bookings',
            'showtime_prices',
            'showtimes',
            'movie_reviews',
            'movie_genres',
            'genres',
            'movies',
            'seats',
            'seat_types',
            'rooms',
            'cinemas',
            'cinema_chains',
            'cities',
            'login_histories',
            'ip_details',
            'social_accounts',
            'role_permissions',
            'permission_actions',
            'permissions',
            'actions',
            'users',
            'roles',
        ] as $table) {
            DB::table($table)->truncate();
        }

        Schema::enableForeignKeyConstraints();
    }

    private function seedAccessControl(): void
    {
        $roles = [
            'super admin' => 'Toan quyen cau hinh he thong, phan quyen va du lieu van hanh.',
            'manager' => 'Quan ly rap, lich chieu, gia ve va bao cao doanh thu.',
            'staff' => 'Xu ly dat ve, soat ve, ho tro khach hang tai quay.',
            'customer' => 'Khach hang dat ve, thanh toan va quan ly thanh vien.',
        ];

        foreach ($roles as $name => $description) {
            $this->roles[$name] = Role::factory()->create(compact('name', 'description'));
        }

        $permissions = [
            'dashboard' => 'Bang dieu khien',
            'users' => 'Nguoi dung',
            'roles' => 'Vai tro',
            'cinemas' => 'Rap chieu',
            'rooms' => 'Phong chieu',
            'seats' => 'Ghe ngoi',
            'movies' => 'Phim',
            'genres' => 'The loai',
            'showtimes' => 'Suat chieu',
            'bookings' => 'Dat ve',
            'payments' => 'Thanh toan',
            'combos' => 'Combo bap nuoc',
            'promotions' => 'Khuyen mai',
            'reports' => 'Bao cao',
        ];

        foreach ($permissions as $key => $name) {
            $this->permissions[$key] = Permission::factory()->create(['key' => $key, 'name' => $name]);
        }

        $actions = [
            'view' => 'Xem',
            'create' => 'Them moi',
            'update' => 'Cap nhat',
            'delete' => 'Xoa',
            'approve' => 'Duyet',
            'export' => 'Xuat du lieu',
        ];

        foreach ($actions as $key => $name) {
            $this->actions[$key] = Action::factory()->create(['key' => $key, 'name' => $name]);
        }

        foreach ($this->permissions as $permission) {
            foreach ($this->actions as $action) {
                DB::table('permission_actions')->insert([
                    'permission_id' => $permission->id,
                    'action_id' => $action->id,
                ]);
            }
        }

        $this->attachRolePermissions('super admin', array_keys($permissions), array_keys($actions));
        $this->attachRolePermissions('manager', [
            'dashboard',
            'cinemas',
            'rooms',
            'seats',
            'movies',
            'genres',
            'showtimes',
            'bookings',
            'payments',
            'combos',
            'promotions',
            'reports',
        ], ['view', 'create', 'update', 'approve', 'export']);
        $this->attachRolePermissions('staff', ['dashboard', 'bookings', 'payments', 'combos', 'showtimes'], ['view', 'create', 'update']);
        $this->attachRolePermissions('customer', ['movies', 'showtimes', 'bookings', 'payments', 'promotions'], ['view', 'create']);
    }

    private function attachRolePermissions(string $roleName, array $permissionKeys, array $actionKeys): void
    {
        foreach ($permissionKeys as $permissionKey) {
            foreach ($actionKeys as $actionKey) {
                DB::table('role_permissions')->insert([
                    'role_id' => $this->roles[$roleName]->id,
                    'permission_id' => $this->permissions[$permissionKey]->id,
                    'action_id' => $this->actions[$actionKey]->id,
                ]);
            }
        }
    }

    private function seedUsers(): void
    {
        $users = [
            ['role' => 'super admin', 'name' => 'Nguyen Minh Quan', 'email' => 'quan.admin@cinemax.vn', 'phone' => '0901000001', 'gender' => 'male', 'date_of_birth' => '1988-02-17'],
            ['role' => 'manager', 'name' => 'Tran Thu Ha', 'email' => 'ha.manager@cinemax.vn', 'phone' => '0901000002', 'gender' => 'female', 'date_of_birth' => '1990-09-12'],
            ['role' => 'manager', 'name' => 'Le Hoang Nam', 'email' => 'nam.manager@cinemax.vn', 'phone' => '0901000003', 'gender' => 'male', 'date_of_birth' => '1986-11-21'],
            ['role' => 'staff', 'name' => 'Pham Gia Bao', 'email' => 'bao.staff@cinemax.vn', 'phone' => '0901000004', 'gender' => 'male', 'date_of_birth' => '1998-03-08'],
            ['role' => 'staff', 'name' => 'Do Minh Anh', 'email' => 'anh.staff@cinemax.vn', 'phone' => '0901000005', 'gender' => 'female', 'date_of_birth' => '1999-07-19'],
            ['role' => 'customer', 'name' => 'Bui Khanh Linh', 'email' => 'linh.bui@example.com', 'phone' => '0912000001', 'gender' => 'female', 'date_of_birth' => '2001-04-11'],
            ['role' => 'customer', 'name' => 'Vo Duc Huy', 'email' => 'huy.vo@example.com', 'phone' => '0912000002', 'gender' => 'male', 'date_of_birth' => '1997-12-29'],
            ['role' => 'customer', 'name' => 'Dang Ngoc Tram', 'email' => 'tram.dang@example.com', 'phone' => '0912000003', 'gender' => 'female', 'date_of_birth' => '1995-05-23'],
            ['role' => 'customer', 'name' => 'Hoang Viet Dung', 'email' => 'dung.hoang@example.com', 'phone' => '0912000004', 'gender' => 'male', 'date_of_birth' => '1993-08-14'],
            ['role' => 'customer', 'name' => 'Mai Phuong Thao', 'email' => 'thao.mai@example.com', 'phone' => '0912000005', 'gender' => 'female', 'date_of_birth' => '2000-10-05'],
            ['role' => 'customer', 'name' => 'Phan Nhat Minh', 'email' => 'minh.phan@example.com', 'phone' => '0912000006', 'gender' => 'male', 'date_of_birth' => '1996-01-30'],
            ['role' => 'customer', 'name' => 'Vu Quynh Chi', 'email' => 'chi.vu@example.com', 'phone' => '0912000007', 'gender' => 'female', 'date_of_birth' => '1998-06-18'],
        ];

        foreach ($users as $index => $user) {
            $this->users[] = User::factory()->create([
                'role_id' => $this->roles[$user['role']]->id,
                'name' => $user['name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'email_verified_at' => now()->subDays(60 - $index),
                'avatar' => 'https://i.pravatar.cc/300?img=' . ($index + 12),
                'date_of_birth' => $user['date_of_birth'],
                'gender' => $user['gender'],
                'is_active' => true,
            ]);
        }

        $lastNames = ['Nguyen', 'Tran', 'Le', 'Pham', 'Hoang', 'Phan', 'Vu', 'Vo', 'Dang', 'Bui', 'Do', 'Ngo'];
        $middleNames = ['Minh', 'Gia', 'Thanh', 'Quoc', 'Bao', 'Ngoc', 'Phuong', 'Khanh', 'Nhat', 'Hoai'];
        $givenNames = ['An', 'Anh', 'Binh', 'Chau', 'Dung', 'Giang', 'Hai', 'Han', 'Hieu', 'Khoa', 'Lam', 'Linh'];

        for ($index = 1; $index <= 120; $index++) {
            $lastName = $lastNames[($index - 1) % count($lastNames)];
            $middleName = $middleNames[(int) floor(($index - 1) / count($lastNames)) % count($middleNames)];
            $givenName = $givenNames[(($index - 1) * 5 + (int) floor(($index - 1) / count($lastNames))) % count($givenNames)];
            $gender = in_array($givenName, ['Anh', 'Chau', 'Giang', 'Han', 'Linh'], true) ? 'female' : 'male';

            $this->users[] = User::factory()->create([
                'role_id' => $this->roles['customer']->id,
                'name' => trim($lastName . ' ' . $middleName . ' ' . $givenName),
                'email' => 'customer' . str_pad((string) $index, 3, '0', STR_PAD_LEFT) . '@cinemax-demo.vn',
                'phone' => '092' . str_pad((string) $index, 7, '0', STR_PAD_LEFT),
                'email_verified_at' => now()->subDays(($index % 90) + 1),
                'avatar' => 'https://i.pravatar.cc/300?u=cinemax-customer-' . $index,
                'date_of_birth' => now()->subYears(18 + ($index % 28))->subDays($index * 7)->toDateString(),
                'gender' => $gender,
                'is_active' => $index % 17 !== 0,
            ]);
        }

        $tiers = ['bronze', 'silver', 'gold', 'platinum'];
        foreach (array_slice($this->users, 5) as $index => $user) {
            Membership::factory()->create([
                'user_id' => $user->id,
                'tier' => $tiers[$index % count($tiers)],
                'points' => 180 + ($index * 470),
            ]);
        }
    }

    private function seedLocations(): void
    {
        foreach (['TP. Ho Chi Minh', 'Ha Noi', 'Da Nang', 'Can Tho', 'Hai Phong'] as $cityName) {
            $this->cities[$cityName] = City::factory()->create(['name' => $cityName]);
        }

        $chains = [
            ['name' => 'Galaxy Cinema', 'logo' => 'https://placehold.co/360x160/df2f2f/ffffff.png?text=Galaxy+Cinema'],
            ['name' => 'BHD Star Cineplex', 'logo' => 'https://placehold.co/360x160/f5b301/111111.png?text=BHD+Star'],
            ['name' => 'Cinestar', 'logo' => 'https://placehold.co/360x160/0f72b8/ffffff.png?text=Cinestar'],
        ];

        foreach ($chains as $chain) {
            $this->chains[$chain['name']] = CinemaChain::factory()->create($chain);
        }

        $cinemas = [
            ['city' => 'TP. Ho Chi Minh', 'chain' => 'Galaxy Cinema', 'name' => 'Galaxy Nguyen Du', 'address' => '116 Nguyen Du, Phuong Ben Thanh, Quan 1'],
            ['city' => 'TP. Ho Chi Minh', 'chain' => 'BHD Star Cineplex', 'name' => 'BHD Star Thao Dien', 'address' => 'Tang 5, Vincom Mega Mall Thao Dien, TP Thu Duc'],
            ['city' => 'Ha Noi', 'chain' => 'Galaxy Cinema', 'name' => 'Galaxy Mipec Long Bien', 'address' => 'Tang 5, Mipec Long Bien, 2 Long Bien 2'],
            ['city' => 'Ha Noi', 'chain' => 'Cinestar', 'name' => 'Cinestar Hai Ba Trung', 'address' => '135 Hai Ba Trung, Quan Hoan Kiem'],
            ['city' => 'Da Nang', 'chain' => 'BHD Star Cineplex', 'name' => 'BHD Star Le Duan', 'address' => '255 Le Duan, Quan Thanh Khe'],
            ['city' => 'Can Tho', 'chain' => 'Cinestar', 'name' => 'Cinestar Ninh Kieu', 'address' => '68 Tran Phu, Quan Ninh Kieu'],
        ];

        foreach ($cinemas as $cinema) {
            $model = Cinema::factory()->create([
                'city_id' => $this->cities[$cinema['city']]->id,
                'cinema_chain_id' => $this->chains[$cinema['chain']]->id,
                'name' => $cinema['name'],
                'address' => $cinema['address'],
            ]);

            $this->cinemas[$cinema['name']] = $model;
        }
    }

    private function seedRoomsAndSeats(): void
    {
        foreach ([
            ['name' => 'Standard', 'base_multiplier' => 1],
            ['name' => 'Couple', 'base_multiplier' => 1.8],
            ['name' => 'VIP', 'base_multiplier' => 1.35],
            ['name' => 'Sweetbox', 'base_multiplier' => 2.1],
        ] as $type) {
            $this->seatTypes[$type['name']] = SeatType::factory()->create($type);
        }

        $roomTypes = ['2D', '3D', 'IMAX', 'VIP'];
        foreach (array_values($this->cinemas) as $cinemaIndex => $cinema) {
            for ($number = 1; $number <= 3; $number++) {
                $room = Room::factory()->create([
                    'cinema_id' => $cinema->id,
                    'name' => 'Phong ' . $number . ' - ' . $cinema->name,
                    'type' => $roomTypes[($cinemaIndex + $number - 1) % count($roomTypes)],
                ]);

                $this->rooms[] = $room;
                $this->seatsByRoom[$room->id] = [];

                foreach (range('A', 'H') as $rowIndex => $rowLabel) {
                    for ($seatNumber = 1; $seatNumber <= 10; $seatNumber++) {
                        $seatTypeName = match (true) {
                            $rowLabel === 'H' && $seatNumber >= 3 && $seatNumber <= 8 => 'Sweetbox',
                            in_array($rowLabel, ['F', 'G'], true) => 'VIP',
                            $rowLabel === 'E' && in_array($seatNumber, [1, 2, 9, 10], true) => 'Couple',
                            default => 'Standard',
                        };

                        $this->seatsByRoom[$room->id][] = Seat::factory()->create([
                            'room_id' => $room->id,
                            'seat_type_id' => $this->seatTypes[$seatTypeName]->id,
                            'row_label' => $rowLabel,
                            'seat_number' => $seatNumber,
                        ]);
                    }
                }
            }
        }
    }

    private function seedMovies(): void
    {
        foreach (['Hanh dong', 'Phieu luu', 'Hoat hinh', 'Tam ly', 'Hai', 'Kinh di', 'Khoa hoc vien tuong', 'Gia dinh', 'Lang man'] as $name) {
            $this->genres[$name] = Genre::factory()->create(['name' => $name]);
        }

        $movies = [
            [
                'title' => 'Dune: Part Two',
                'duration_minutes' => 166,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/1pdfLvkbY9ohJlCjQH2CZjjYVvJ.jpg',
                'banner_url' => 'https://image.tmdb.org/t/p/w1280/xOMo8BRK7PfcJv9JCnx7s5hj0PX.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=Way9Dexny3w',
                'description' => 'Paul Atreides hop luc cung nguoi Fremen de doi dau the luc da huy hoai gia toc minh.',
                'content' => 'Mot chuyen phieu luu khoa hoc vien tuong quy mo lon ve long trung thanh, quyen luc va cai gia cua so menh.',
                'release_date' => '2024-03-01',
                'rating' => 'T13',
                'language' => 'English',
                'status' => 'ended',
                'genres' => ['Khoa hoc vien tuong', 'Phieu luu', 'Hanh dong'],
            ],
            [
                'title' => 'Inside Out 2',
                'duration_minutes' => 96,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/vpnVM9B6NMmQpWeZvzLvDESb2QY.jpg',
                'banner_url' => 'https://image.tmdb.org/t/p/w1280/stKGOm8UyhuLPR9sZLjs5AkmncA.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=LEjhY15eCx0',
                'description' => 'Riley buoc vao tuoi teen voi nhung cam xuc moi xuat hien day bat ngo.',
                'content' => 'Bo phim hoat hinh am ap ve truong thanh, gia dinh va cach ta hoc cach song chung voi nhung cam xuc phuc tap.',
                'release_date' => '2024-06-14',
                'rating' => 'P',
                'language' => 'English',
                'status' => 'now_showing',
                'genres' => ['Hoat hinh', 'Gia dinh', 'Hai'],
            ],
            [
                'title' => 'Oppenheimer',
                'duration_minutes' => 181,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/8Gxv8gSFCU0XGDykEGv7zR1n2ua.jpg',
                'banner_url' => 'https://image.tmdb.org/t/p/w1280/fm6KqXpk3M2HVveHwCrBSSBaO0V.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=uYPbbksJxIg',
                'description' => 'Chan dung J. Robert Oppenheimer va nhung quyet dinh lam thay doi lich su nhan loai.',
                'content' => 'Tac pham tam ly lich su cang thang, tap trung vao tham vong khoa hoc va ganh nang dao duc.',
                'release_date' => '2023-07-21',
                'rating' => 'T16',
                'language' => 'English',
                'status' => 'ended',
                'genres' => ['Tam ly'],
            ],
            [
                'title' => 'Mai',
                'duration_minutes' => 131,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/9t7YTHp0ZbEl1BNgAu5Y9Hhvu0j.jpg',
                'banner_url' => 'https://image.tmdb.org/t/p/w1280/6uKobmMZa0E47l5YUYJZfK87Ilh.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=EX6clvId19s',
                'description' => 'Mot nguoi phu nu tung trai tim thay hy vong moi giua nhung dinh kien doi thuong.',
                'content' => 'Cau chuyen tinh cam Viet Nam ve tinh yeu, gia dinh va noi dau can duoc lang nghe.',
                'release_date' => '2024-02-10',
                'rating' => 'T18',
                'language' => 'Vietnamese',
                'status' => 'now_showing',
                'genres' => ['Tam ly', 'Lang man'],
            ],
            [
                'title' => 'Godzilla x Kong: The New Empire',
                'duration_minutes' => 115,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/z1p34vh7dEOnLDmyCrlUVLuoDzd.jpg',
                'banner_url' => 'https://image.tmdb.org/t/p/w1280/1XDDXPXGiI8id7MrUxK36ke7gkX.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=lV1OOlGwExM',
                'description' => 'Hai bieu tuong khong lo hop suc truoc moi de doa tu the gioi rong lon ben duoi Trai Dat.',
                'content' => 'Phim giai tri toc do cao voi chien truong quai vat, hinh anh hoanh trang va nhieu canh hanh dong.',
                'release_date' => '2024-03-29',
                'rating' => 'T13',
                'language' => 'English',
                'status' => 'now_showing',
                'genres' => ['Hanh dong', 'Phieu luu', 'Khoa hoc vien tuong'],
            ],
            [
                'title' => 'Kung Fu Panda 4',
                'duration_minutes' => 94,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/kDp1vUBnMpe8ak4rjgl3cLELqjU.jpg',
                'banner_url' => 'https://image.tmdb.org/t/p/w1280/kYgQzzjNis5jJalYtIHgrom0gOx.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=_inKs4eeHiI',
                'description' => 'Po tim nguoi ke nhiem Dragon Warrior va doi dau mot ke thu co kha nang bien hinh.',
                'content' => 'Chuyen phieu luu hai huoc, sang mau, phu hop gia dinh va khan gia nho tuoi.',
                'release_date' => '2024-03-08',
                'rating' => 'P',
                'language' => 'English',
                'status' => 'now_showing',
                'genres' => ['Hoat hinh', 'Gia dinh', 'Hai'],
            ],
            [
                'title' => 'A Quiet Place: Day One',
                'duration_minutes' => 99,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/yrpPYKijwdMHyTGIOd1iK1h0Xno.jpg',
                'banner_url' => 'https://image.tmdb.org/t/p/w1280/4yrOyO3N55XazHQXXYoqiiPQd40.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=YPY7J-flzE8',
                'description' => 'Ngay dau tien cua tham hoa, khi im lang tro thanh cach duy nhat de song sot.',
                'content' => 'Bo phim kinh di cang thang voi khong khi do thi hon loan va nhung khoang lang day ap luc.',
                'release_date' => '2024-06-28',
                'rating' => 'T16',
                'language' => 'English',
                'status' => 'coming_soon',
                'genres' => ['Kinh di', 'Khoa hoc vien tuong'],
            ],
            [
                'title' => 'Deadpool & Wolverine',
                'duration_minutes' => 127,
                'poster_url' => 'https://image.tmdb.org/t/p/w500/8cdWjvZQUExUUTzyp4t6EDMubfO.jpg',
                'banner_url' => 'https://image.tmdb.org/t/p/w1280/yDHYTfA3R0jFYba16jBB1ef8oIt.jpg',
                'trailer_url' => 'https://www.youtube.com/watch?v=73_1biulkYk',
                'description' => 'Deadpool keo Wolverine vao mot nhiem vu hon loan, vua bao luc vua day mau sac hai huoc.',
                'content' => 'Phim sieu anh hung co tiet tau nhanh, nhieu tinh huong tu trao va cac man doi dap sac canh.',
                'release_date' => '2024-07-26',
                'rating' => 'T18',
                'language' => 'English',
                'status' => 'coming_soon',
                'genres' => ['Hanh dong', 'Hai', 'Khoa hoc vien tuong'],
            ],
        ];

        foreach ($movies as $movie) {
            $genreNames = $movie['genres'];
            unset($movie['genres']);

            $model = Movie::factory()->create([
                ...$movie,
                'slug' => Str::slug($movie['title']),
            ]);

            foreach ($genreNames as $genreName) {
                DB::table('movie_genres')->insert([
                    'movie_id' => $model->id,
                    'genre_id' => $this->genres[$genreName]->id,
                ]);
            }

            $this->movies[] = $model;
        }
    }

    private function seedMovieReviews(): void
    {
        $customers = array_values(array_filter($this->users, fn (User $user) => $user->role_id === $this->roles['customer']->id));
        $comments = [
            'Phim co nhip ke chuyen cuon, hinh anh dep va am thanh rat da.',
            'Noi dung on, trai nghiem ngoai rap tot hon mong doi.',
            'Dien vien dien tron vai, mot vai phan hoi dai nhung van dang xem.',
            'Minh thich cach phim xay dung cam xuc va cao trao.',
            'Phu hop xem cuoi tuan, giai tri tot va de theo doi.',
            'Canh hanh dong an tuong, phan am nhac nang tam trai nghiem.',
            'Phim khong qua moi nhung van lam minh hai long.',
            'Tiet tau gon, hinh anh sang va co nhieu khoanh khac dang nho.',
        ];

        foreach ($this->movies as $movieIndex => $movie) {
            $reviewCount = 10 + ($movieIndex % 5);

            for ($index = 0; $index < $reviewCount; $index++) {
                $customer = $customers[($movieIndex * 13 + $index) % count($customers)];
                $score = min(10, 6.8 + (($movieIndex + $index) % 7) * 0.4);
                $status = $index % 11 === 0 ? 'pending' : ($index % 13 === 0 ? 'rejected' : 'approved');

                DB::table('movie_reviews')->insert([
                    'id' => (string) Str::uuid7(),
                    'movie_id' => $movie->id,
                    'user_id' => $customer->id,
                    'rating_score' => $score,
                    'comment' => $comments[($movieIndex + $index) % count($comments)],
                    'status' => $status,
                    'created_at' => now()->subDays(($movieIndex * 3) + $index),
                    'updated_at' => now()->subDays(($movieIndex * 3) + $index),
                ]);
            }

            $approvedReviews = DB::table('movie_reviews')
                ->where('movie_id', $movie->id)
                ->where('status', 'approved');

            $movie->update([
                'rating_score' => round((float) $approvedReviews->avg('rating_score'), 1),
                'rating_count' => (clone $approvedReviews)->count(),
            ]);
        }
    }

    private function seedShowtimes(): void
    {
        $timeSlots = ['09:15', '12:30', '15:45', '18:20', '21:10'];
        $activeMovies = array_values(array_filter($this->movies, fn (Movie $movie) => $movie->status !== 'ended'));
        $startDate = Carbon::today()->subDays(2);

        foreach ($this->rooms as $roomIndex => $room) {
            for ($day = 0; $day < 7; $day++) {
                foreach (array_slice($timeSlots, 0, 3) as $slotIndex => $slot) {
                    $movie = $activeMovies[($roomIndex + $day + $slotIndex) % count($activeMovies)];
                    $start = $startDate->copy()->addDays($day)->setTimeFromTimeString($slot);
                    $basePrice = 85000 + (($roomIndex + $slotIndex) % 4 * 10000);
                    $status = $start->isPast() ? 'completed' : ($slotIndex === 2 && $day === 0 ? 'ongoing' : 'scheduled');

                    $showtime = Showtime::factory()->create([
                        'movie_id' => $movie->id,
                        'room_id' => $room->id,
                        'show_date' => $start->toDateString(),
                        'start_time' => $start,
                        'end_time' => $start->copy()->addMinutes($movie->duration_minutes + 20),
                        'base_price' => $basePrice,
                        'status' => $status,
                    ]);

                    foreach ($this->seatTypes as $seatType) {
                        ShowtimePrice::factory()->create([
                            'showtime_id' => $showtime->id,
                            'seat_type_id' => $seatType->id,
                            'price' => round($basePrice * (float) $seatType->base_multiplier, -3),
                        ]);
                    }

                    $this->showtimes[] = $showtime->load('movie', 'room');
                }
            }
        }
    }

    private function seedCombos(): void
    {
        $comboTemplates = [
            ['name' => 'Classic Popcorn Set', 'description' => '1 bap lon vi bo, 2 nuoc ngot size M.', 'price' => 89000, 'image_query' => 'popcorn,soda'],
            ['name' => 'Couple Nachos Set', 'description' => '1 nachos pho mai, 1 bap vua, 2 tra dao.', 'price' => 129000, 'image_query' => 'nachos,cinema-snack'],
            ['name' => 'Family Movie Feast', 'description' => '2 bap lon, 4 nuoc ngot, 2 hotdog.', 'price' => 219000, 'image_query' => 'hotdog,popcorn'],
            ['name' => 'Premium Snack Box', 'description' => 'Bap caramel, ga vien, khoai tay va soda chanh.', 'price' => 159000, 'image_query' => 'popcorn,snack-box'],
        ];

        foreach (array_values($this->cinemas) as $cinemaIndex => $cinema) {
            $this->combosByCinema[$cinema->id] = [];

            foreach ($comboTemplates as $index => $combo) {
                $imageLock = 700 + ($cinemaIndex * 10) + $index;
                $model = Combo::factory()->create([
                    'cinema_id' => $cinema->id,
                    'name' => $combo['name'] . ' - ' . str_replace(['Galaxy ', 'BHD Star ', 'Cinestar '], '', $cinema->name),
                    'description' => $combo['description'],
                    'price' => $combo['price'] + ($index * 5000),
                    'status' => $index === 3 ? 'inactive' : 'active',
                    'image' => 'https://loremflickr.com/900/600/' . $combo['image_query'] . '?lock=' . $imageLock,
                ]);

                $this->combosByCinema[$cinema->id][] = $model;
            }
        }
    }

    private function seedPromotions(): void
    {
        $promotions = [
            ['code' => 'WELCOME50K', 'description' => 'Giam 50.000 VND cho khach hang moi.', 'discount_type' => 'fixed_amount', 'discount_value' => 50000, 'applicable_to' => 'booking'],
            ['code' => 'WEEKDAY15', 'description' => 'Giam 15% cho suat chieu thu 2 den thu 5.', 'discount_type' => 'percentage', 'discount_value' => 15, 'applicable_to' => 'ticket'],
            ['code' => 'COMBO20', 'description' => 'Giam 20% khi mua combo bap nuoc.', 'discount_type' => 'percentage', 'discount_value' => 20, 'applicable_to' => 'combo'],
            ['code' => 'FAMILYDAY', 'description' => 'Uu dai gia dinh cuoi tuan.', 'discount_type' => 'fixed_amount', 'discount_value' => 80000, 'applicable_to' => 'booking'],
        ];

        foreach ($promotions as $index => $promotion) {
            $this->promotions[] = Promotion::factory()->create([
                ...$promotion,
                'start_date' => now()->subDays(14 + $index),
                'end_date' => now()->addDays(45 + ($index * 10)),
                'usage_limit' => 500 + ($index * 100),
                'per_user_limit' => 2,
                'status' => 'active',
            ]);
        }
    }

    private function seedBookings(): void
    {
        $customers = array_values(array_filter($this->users, fn (User $user) => $user->role_id === $this->roles['customer']->id));
        $confirmedShowtimes = array_values(array_filter($this->showtimes, fn (Showtime $showtime) => $showtime->status !== 'cancelled'));
        $usedSeatKeys = [];

        foreach (range(0, 149) as $index) {
            $user = $customers[$index % count($customers)];
            $showtime = $confirmedShowtimes[($index * 5) % count($confirmedShowtimes)];
            $room = $showtime->room;
            $movie = $showtime->movie;
            $seats = $this->pickSeatsForBooking($showtime, 2 + ($index % 3), $usedSeatKeys);
            $ticketTotal = 0;
            $status = match ($index % 10) {
                0 => 'pending',
                6 => 'cancelled',
                8 => 'expired',
                default => 'confirmed',
            };

            $booking = Booking::factory()->create([
                'user_id' => $user->id,
                'showtime_id' => $showtime->id,
                'booking_code' => 'BK' . now()->format('ymd') . str_pad((string) ($index + 1), 5, '0', STR_PAD_LEFT),
                'total_amount' => 0,
                'status' => $status,
                'cancellation_reason' => $status === 'cancelled' ? 'Khach hang doi lich xem phim.' : null,
                'cancelled_at' => $status === 'cancelled' ? now()->subDays(1) : null,
                'expired_at' => $status === 'expired' ? now()->subHours(8) : now()->addMinutes(15),
                'confirmed_at' => $status === 'confirmed' ? now()->subDays(3)->addHours($index) : null,
            ]);

            foreach ($seats as $seat) {
                $price = ShowtimePrice::query()
                    ->where('showtime_id', $showtime->id)
                    ->where('seat_type_id', $seat->seat_type_id)
                    ->value('price');
                $ticketTotal += (float) $price;

                BookingItem::factory()->create([
                    'booking_id' => $booking->id,
                    'seat_id' => $seat->id,
                    'price' => $price,
                    'seat_type_name' => $seat->seatType->name,
                    'movie_title' => $movie->title,
                    'room_name' => $room->name,
                    'seat_label' => $seat->row_label . $seat->seat_number,
                ]);
            }

            $comboTotal = $this->attachBookingCombos($booking, $room->cinema_id, $index);
            $discount = $this->attachPromotionUsage($booking, $user, $index, $ticketTotal + $comboTotal);
            $booking->update(['total_amount' => max(0, $ticketTotal + $comboTotal - $discount)]);

            $this->createBookingLifecycle($booking, $status);
            $this->createPaymentRecords($booking, $status, $index);
        }

        foreach (array_slice($confirmedShowtimes, 0, 10) as $index => $showtime) {
            $seat = $this->seatsByRoom[$showtime->room_id][30 + $index];
            SeatHold::factory()->create([
                'user_id' => $customers[$index % count($customers)]->id,
                'showtime_id' => $showtime->id,
                'seat_id' => $seat->id,
                'expired_at' => now()->addMinutes(10 + $index),
            ]);
        }
    }

    private function pickSeatsForBooking(Showtime $showtime, int $count, array &$usedSeatKeys): array
    {
        $seats = $this->seatsByRoom[$showtime->room_id];
        $picked = [];

        foreach ($seats as $seat) {
            $key = $showtime->id . ':' . $seat->id;
            if (isset($usedSeatKeys[$key])) {
                continue;
            }

            $usedSeatKeys[$key] = true;
            $picked[] = $seat->load('seatType');

            if (count($picked) === $count) {
                return $picked;
            }
        }

        return $picked;
    }

    private function attachBookingCombos(Booking $booking, string $cinemaId, int $index): float
    {
        $comboTotal = 0;
        $combos = array_values(array_filter($this->combosByCinema[$cinemaId], fn (Combo $combo) => $combo->status === 'active'));

        foreach (array_slice($combos, 0, 1 + ($index % 2)) as $comboIndex => $combo) {
            $quantity = 1 + (($index + $comboIndex) % 2);
            $total = (float) $combo->price * $quantity;

            DB::table('booking_combos')->insert([
                'id' => (string) Str::uuid7(),
                'booking_id' => $booking->id,
                'combo_id' => $combo->id,
                'combo_name' => $combo->name,
                'quantity' => $quantity,
                'unit_price' => $combo->price,
                'total_price' => $total,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $comboTotal += $total;
        }

        return $comboTotal;
    }

    private function attachPromotionUsage(Booking $booking, User $user, int $index, float $subtotal): float
    {
        if ($index % 3 !== 0) {
            return 0;
        }

        $promotion = $this->promotions[$index % count($this->promotions)];
        $discount = $promotion->discount_type === 'percentage'
            ? round($subtotal * ((float) $promotion->discount_value / 100), -3)
            : (float) $promotion->discount_value;

        DB::table('promotion_usages')->insert([
            'id' => (string) Str::uuid7(),
            'promotion_id' => $promotion->id,
            'user_id' => $user->id,
            'booking_id' => $booking->id,
            'discount_amount' => min($discount, $subtotal),
            'used_at' => now()->subDays($index % 6),
        ]);

        return min($discount, $subtotal);
    }

    private function createBookingLifecycle(Booking $booking, string $status): void
    {
        DB::table('booking_status_logs')->insert([
            'id' => (string) Str::uuid7(),
            'booking_id' => $booking->id,
            'old_status' => null,
            'new_status' => 'pending',
            'changed_at' => $booking->created_at,
        ]);

        if ($status !== 'pending') {
            DB::table('booking_status_logs')->insert([
                'id' => (string) Str::uuid7(),
                'booking_id' => $booking->id,
                'old_status' => 'pending',
                'new_status' => $status,
                'changed_at' => now()->subDays(2),
            ]);
        }
    }

    private function createPaymentRecords(Booking $booking, string $bookingStatus, int $index): void
    {
        $provider = ['vnpay', 'momo', 'zalopay', 'cashier'][$index % 4];
        $paymentStatus = match ($bookingStatus) {
            'confirmed' => 'paid',
            'cancelled' => 'refunded',
            'expired' => 'failed',
            default => 'pending',
        };

        DB::table('payment_attempts')->insert([
            'id' => (string) Str::uuid7(),
            'booking_id' => $booking->id,
            'provider' => $provider,
            'request_payload' => json_encode(['amount' => $booking->total_amount, 'booking_code' => $booking->booking_code]),
            'response_payload' => json_encode(['gateway_ref' => strtoupper($provider) . '-' . str_pad((string) ($index + 1), 8, '0', STR_PAD_LEFT)]),
            'status' => $paymentStatus === 'paid' || $paymentStatus === 'refunded' ? 'success' : ($paymentStatus === 'failed' ? 'failed' : 'pending'),
            'created_at' => now(),
        ]);

        Payment::factory()->create([
            'booking_id' => $booking->id,
            'provider' => $provider,
            'transaction_code' => strtoupper($provider) . '-' . now()->format('ymd') . '-' . str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT),
            'amount' => $booking->total_amount,
            'status' => $paymentStatus,
            'paid_at' => in_array($paymentStatus, ['paid', 'refunded'], true) ? now()->subDays($index % 5) : null,
            'idempotency_key' => 'idem-' . $booking->booking_code,
            'refunded_amount' => $paymentStatus === 'refunded' ? $booking->total_amount : null,
            'refund_status' => $paymentStatus === 'refunded' ? 'completed' : null,
        ]);
    }

    private function seedActivityLogs(): void
    {
        $admins = array_slice($this->users, 0, 5);
        $targets = [
            ['action' => 'created', 'type' => Movie::class, 'items' => array_slice($this->movies, 0, 4)],
            ['action' => 'updated', 'type' => Showtime::class, 'items' => array_slice($this->showtimes, 0, 8)],
            ['action' => 'approved', 'type' => Promotion::class, 'items' => $this->promotions],
        ];

        foreach ($targets as $groupIndex => $group) {
            foreach ($group['items'] as $index => $item) {
                ActivityLog::factory()->create([
                    'user_id' => $admins[($groupIndex + $index) % count($admins)]->id,
                    'action' => $group['action'],
                    'entity_type' => $group['type'],
                    'entity_id' => $item->id,
                    'metadata' => json_encode([
                        'ip' => '113.161.72.' . (40 + $index),
                        'note' => 'Du lieu mau duoc tao boi database seeder.',
                    ]),
                    'created_at' => now()->subHours(30 - $index),
                    'updated_at' => now()->subHours(30 - $index),
                ]);
            }
        }
    }

    private function seedLoginData(): void
    {
        $ipIds = [];
        foreach ([
            ['ip' => '113.161.72.41', 'city' => 'Ho Chi Minh City', 'region' => 'Ho Chi Minh', 'country' => 'VN', 'loc' => '10.8231,106.6297', 'org' => 'VNPT Corp', 'timezone' => 'Asia/Ho_Chi_Minh'],
            ['ip' => '14.177.81.22', 'city' => 'Ha Noi', 'region' => 'Ha Noi', 'country' => 'VN', 'loc' => '21.0278,105.8342', 'org' => 'Viettel Group', 'timezone' => 'Asia/Ho_Chi_Minh'],
            ['ip' => '42.116.98.73', 'city' => 'Da Nang', 'region' => 'Da Nang', 'country' => 'VN', 'loc' => '16.0471,108.2068', 'org' => 'FPT Telecom', 'timezone' => 'Asia/Ho_Chi_Minh'],
        ] as $ip) {
            $id = (string) Str::uuid7();
            $ipIds[] = $id;
            DB::table('ip_details')->insert([
                'id' => $id,
                ...$ip,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        foreach (array_slice($this->users, 5, 4) as $index => $user) {
            DB::table('social_accounts')->insert([
                'id' => (string) Str::uuid7(),
                'user_id' => $user->id,
                'provider' => $index % 2 === 0 ? 'google' : 'facebook',
                'provider_id' => 'social-' . strtolower(Str::slug($user->name)) . '-' . ($index + 1),
                'created_at' => now()->subDays(20 - $index),
                'updated_at' => now()->subDays(20 - $index),
            ]);
        }

        foreach ($this->users as $index => $user) {
            DB::table('login_histories')->insert([
                'id' => (string) Str::uuid7(),
                'user_id' => $user->id,
                'ip_id' => $ipIds[$index % count($ipIds)],
                'login_at' => now()->subHours(4 + $index),
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/124.0 Safari/537.36',
                'device' => $index % 3 === 0 ? 'Desktop' : 'Mobile',
                'browser' => $index % 2 === 0 ? 'Chrome' : 'Safari',
                'platform' => $index % 3 === 0 ? 'Windows' : 'iOS',
                'created_at' => now()->subHours(4 + $index),
            ]);
        }
    }
}
