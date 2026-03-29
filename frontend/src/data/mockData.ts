export interface Movie {
  id: string;
  title: string;
  titleVi: string;
  poster: string;
  backdrop: string;
  rating: number;
  duration: number;
  genres: string[];
  genresVi: string[];
  releaseDate: string;
  description: string;
  descriptionVi: string;
  director: string;
  cast: string[];
  trailerUrl: string;
  videoUrl?: string;
  ageRating: string;
  status: 'now-playing' | 'coming-soon';
}

export interface Showtime {
  id: string;
  movieId: string;
  cinemaId: string;
  time: string;
  date: string;
  format: '2D' | '3D' | 'IMAX';
  price: number;
}

export interface Cinema {
  id: string;
  name: string;
  address: string;
  city: string;
  image: string;
  amenities: string[];
}

export interface Combo {
  id: string;
  name: string;
  nameVi: string;
  description: string;
  descriptionVi: string;
  price: number;
  image: string;
}

export interface Booking {
  id: string;
  movieId: string;
  showtimeId: string;
  seats: string[];
  combos?: { comboId: string; quantity: number }[];
  totalPrice: number;
  bookingDate: string;
  status: 'confirmed' | 'cancelled';
}

export const MOVIES: Movie[] = [
  {
    id: '1',
    title: 'Dune: Part Two',
    titleVi: 'Hành Tinh Cát: Phần Hai',
    poster: 'https://picsum.photos/seed/dune2/600/900',
    backdrop: 'https://picsum.photos/seed/dune2-bg/1920/1080',
    rating: 8.9,
    duration: 166,
    genres: ['Action', 'Adventure', 'Sci-Fi'],
    genresVi: ['Hành động', 'Phiêu lưu', 'Viễn tưởng'],
    releaseDate: '2024-03-01',
    description: 'Paul Atreides unites with Chani and the Fremen while on a warpath of revenge against the conspirators who destroyed his family.',
    descriptionVi: 'Paul Atreides hợp lực với Chani và người Fremen khi đang trên con đường trả thù những kẻ âm mưu đã tiêu diệt gia đình mình.',
    director: 'Denis Villeneuve',
    cast: ['Timothée Chalamet', 'Zendaya', 'Rebecca Ferguson'],
    trailerUrl: 'https://www.youtube.com/embed/Way9Dexny3w',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-stars-in-the-night-sky-11641-large.mp4',
    ageRating: 'T13',
    status: 'now-playing',
  },
  {
    id: '2',
    title: 'Oppenheimer',
    titleVi: 'Oppenheimer',
    poster: 'https://picsum.photos/seed/opp/600/900',
    backdrop: 'https://picsum.photos/seed/opp-bg/1920/1080',
    rating: 8.4,
    duration: 180,
    genres: ['Biography', 'Drama', 'History'],
    genresVi: ['Tiểu sử', 'Kịch', 'Lịch sử'],
    releaseDate: '2023-07-21',
    description: 'The story of American scientist J. Robert Oppenheimer and his role in the development of the atomic bomb.',
    descriptionVi: 'Câu chuyện về nhà khoa học Mỹ J. Robert Oppenheimer và vai trò của ông trong việc phát triển bom nguyên tử.',
    director: 'Christopher Nolan',
    cast: ['Cillian Murphy', 'Emily Blunt', 'Matt Damon'],
    trailerUrl: 'https://www.youtube.com/embed/uYPbbksJxIg',
    videoUrl: 'https://assets.mixkit.co/videos/preview/mixkit-fireworks-at-the-beach-11642-large.mp4',
    ageRating: 'T18',
    status: 'now-playing',
  },
  {
    id: '3',
    title: 'Spider-Man: Across the Spider-Verse',
    titleVi: 'Người Nhện: Du Hành Vũ Trụ Nhện',
    poster: 'https://picsum.photos/seed/spiderman/600/900',
    backdrop: 'https://picsum.photos/seed/spiderman-bg/1920/1080',
    rating: 8.6,
    duration: 140,
    genres: ['Animation', 'Action', 'Adventure'],
    genresVi: ['Hoạt hình', 'Hành động', 'Phiêu lưu'],
    releaseDate: '2023-06-02',
    description: 'Miles Morales catapults across the Multiverse, where he encounters a team of Spider-People charged with protecting its very existence.',
    descriptionVi: 'Miles Morales du hành qua Đa vũ trụ, nơi anh gặp một nhóm Người Nhện có nhiệm vụ bảo vệ sự tồn tại của nó.',
    director: 'Joaquim Dos Santos',
    cast: ['Shameik Moore', 'Hailee Steinfeld', 'Oscar Isaac'],
    trailerUrl: 'https://www.youtube.com/embed/shW9i6k8cB0',
    ageRating: 'P',
    status: 'now-playing',
  },
  {
    id: '4',
    title: 'The Batman',
    titleVi: 'The Batman',
    poster: 'https://picsum.photos/seed/batman/600/900',
    backdrop: 'https://picsum.photos/seed/batman-bg/1920/1080',
    rating: 7.8,
    duration: 176,
    genres: ['Action', 'Crime', 'Drama'],
    genresVi: ['Hành động', 'Tội phạm', 'Kịch'],
    releaseDate: '2022-03-04',
    description: 'When a sadistic serial killer begins murdering key political figures in Gotham, Batman is forced to investigate the city\'s hidden corruption.',
    descriptionVi: 'Khi một kẻ giết người hàng loạt tàn bạo bắt đầu sát hại các nhân vật chính trị quan trọng ở Gotham, Batman buộc phải điều tra sự tham nhũng ẩn giấu của thành phố.',
    director: 'Matt Reeves',
    cast: ['Robert Pattinson', 'Zoë Kravitz', 'Jeffrey Wright'],
    trailerUrl: 'https://www.youtube.com/embed/mqqft22076M',
    ageRating: 'T16',
    status: 'now-playing',
  },
  {
    id: '5',
    title: 'Interstellar',
    titleVi: 'Hố Đen Tử Thần',
    poster: 'https://picsum.photos/seed/inter/600/900',
    backdrop: 'https://picsum.photos/seed/inter-bg/1920/1080',
    rating: 8.7,
    duration: 169,
    genres: ['Adventure', 'Drama', 'Sci-Fi'],
    genresVi: ['Phiêu lưu', 'Kịch', 'Viễn tưởng'],
    releaseDate: '2014-11-07',
    description: 'A team of explorers travel through a wormhole in space in an attempt to ensure humanity\'s survival.',
    descriptionVi: 'Một nhóm thám hiểm du hành qua một lỗ sâu trong không gian nhằm nỗ lực đảm bảo sự sống còn của nhân loại.',
    director: 'Christopher Nolan',
    cast: ['Matthew McConaughey', 'Anne Hathaway', 'Jessica Chastain'],
    trailerUrl: 'https://www.youtube.com/embed/zSWdZVtXT7E',
    ageRating: 'P',
    status: 'now-playing',
  },
  {
    id: '6',
    title: 'Inception',
    titleVi: 'Kẻ Đánh Cắp Giấc Mơ',
    poster: 'https://picsum.photos/seed/incep/600/900',
    backdrop: 'https://picsum.photos/seed/incep-bg/1920/1080',
    rating: 8.8,
    duration: 148,
    genres: ['Action', 'Adventure', 'Sci-Fi'],
    genresVi: ['Hành động', 'Phiêu lưu', 'Viễn tưởng'],
    releaseDate: '2010-07-16',
    description: 'A thief who steals corporate secrets through the use of dream-sharing technology is given the inverse task of planting an idea into the mind of a C.E.O.',
    descriptionVi: 'Một kẻ trộm đánh cắp bí mật của công ty thông qua việc sử dụng công nghệ chia sẻ giấc mơ được giao nhiệm vụ ngược lại là gieo rắc một ý tưởng vào tâm trí của một C.E.O.',
    director: 'Christopher Nolan',
    cast: ['Leonardo DiCaprio', 'Joseph Gordon-Levitt', 'Elliot Page'],
    trailerUrl: 'https://www.youtube.com/embed/YoHD9XEInc0',
    ageRating: 'T13',
    status: 'now-playing',
  },
  {
    id: '7',
    title: 'The Dark Knight',
    titleVi: 'Kỵ Sĩ Bóng Đêm',
    poster: 'https://picsum.photos/seed/tdk/600/900',
    backdrop: 'https://picsum.photos/seed/tdk-bg/1920/1080',
    rating: 9.0,
    duration: 152,
    genres: ['Action', 'Crime', 'Drama'],
    genresVi: ['Hành động', 'Tội phạm', 'Kịch'],
    releaseDate: '2008-07-18',
    description: 'When the menace known as the Joker wreaks havoc and chaos on the people of Gotham, Batman must accept one of the greatest psychological and physical tests of his ability to fight injustice.',
    descriptionVi: 'Khi mối đe dọa được gọi là Joker tàn phá và gây hỗn loạn cho người dân Gotham, Batman phải chấp nhận một trong những bài kiểm tra tâm lý và thể chất lớn nhất về khả năng chống lại sự bất công của mình.',
    director: 'Christopher Nolan',
    cast: ['Christian Bale', 'Heath Ledger', 'Aaron Eckhart'],
    trailerUrl: 'https://www.youtube.com/embed/EXeTwQWaywY',
    ageRating: 'T16',
    status: 'now-playing',
  },
  {
    id: '8',
    title: 'Joker: Folie à Deux',
    titleVi: 'Joker: Điên Có Đôi',
    poster: 'https://picsum.photos/seed/joker2/600/900',
    backdrop: 'https://picsum.photos/seed/joker2-bg/1920/1080',
    rating: 0,
    duration: 138,
    genres: ['Crime', 'Drama', 'Musical'],
    genresVi: ['Tội phạm', 'Kịch', 'Nhạc kịch'],
    releaseDate: '2024-10-04',
    description: 'Sequel to the 2019 film Joker.',
    descriptionVi: 'Phần tiếp theo của bộ phim Joker năm 2019.',
    director: 'Todd Phillips',
    cast: ['Joaquin Phoenix', 'Lady Gaga'],
    trailerUrl: 'https://www.youtube.com/embed/xy8aJw1vYHo',
    ageRating: 'T18',
    status: 'coming-soon',
  },
  {
    id: '9',
    title: 'Gladiator II',
    titleVi: 'Võ Sĩ Giác Đấu II',
    poster: 'https://picsum.photos/seed/glad2/600/900',
    backdrop: 'https://picsum.photos/seed/glad2-bg/1920/1080',
    rating: 0,
    duration: 150,
    genres: ['Action', 'Adventure', 'Drama'],
    genresVi: ['Hành động', 'Phiêu lưu', 'Kịch'],
    releaseDate: '2024-11-22',
    description: 'Follows Lucius, the son of Lucilla, after the events of Gladiator.',
    descriptionVi: 'Theo chân Lucius, con trai của Lucilla, sau các sự kiện của Gladiator.',
    director: 'Ridley Scott',
    cast: ['Paul Mescal', 'Denzel Washington', 'Pedro Pascal'],
    trailerUrl: 'https://www.youtube.com/embed/4rgYUipGJNo',
    ageRating: 'T16',
    status: 'coming-soon',
  }
];

export const CINEMAS: Cinema[] = [
  { 
    id: 'c1', 
    name: 'CineMax Landmark 81', 
    address: 'Binh Thanh District, HCMC',
    city: 'HCMC',
    image: 'https://picsum.photos/seed/cinema1/800/600',
    amenities: ['IMAX', 'Gold Class', 'Dolby Atmos']
  },
  { 
    id: 'c2', 
    name: 'CineMax Vincom Center', 
    address: 'District 1, HCMC',
    city: 'HCMC',
    image: 'https://picsum.photos/seed/cinema2/800/600',
    amenities: ['4DX', 'Sweetbox', 'Dolby Atmos']
  },
  { 
    id: 'c3', 
    name: 'CineMax Crescent Mall', 
    address: 'District 7, HCMC',
    city: 'HCMC',
    image: 'https://picsum.photos/seed/cinema3/800/600',
    amenities: ['IMAX', 'Sweetbox']
  },
  { 
    id: 'c4', 
    name: 'CineMax Vincom Ba Trieu', 
    address: 'Hai Ba Trung District, Hanoi',
    city: 'Hanoi',
    image: 'https://picsum.photos/seed/cinema4/800/600',
    amenities: ['IMAX', 'Gold Class', 'Dolby Atmos']
  },
  { 
    id: 'c5', 
    name: 'CineMax Royal City', 
    address: 'Thanh Xuan District, Hanoi',
    city: 'Hanoi',
    image: 'https://picsum.photos/seed/cinema5/800/600',
    amenities: ['4DX', 'Sweetbox']
  },
  { 
    id: 'c6', 
    name: 'CineMax Lotte Mall West Lake', 
    address: 'Tay Ho District, Hanoi',
    city: 'Hanoi',
    image: 'https://picsum.photos/seed/cinema6/800/600',
    amenities: ['IMAX', 'Gold Class', 'Dolby Atmos', '4DX']
  },
];

export const SHOWTIMES: Showtime[] = [
  { id: 's1', movieId: '1', cinemaId: 'c1', time: '10:00', date: '2024-05-20', format: 'IMAX', price: 150000 },
  { id: 's2', movieId: '1', cinemaId: 'c1', time: '14:30', date: '2024-05-20', format: 'IMAX', price: 150000 },
  { id: 's3', movieId: '1', cinemaId: 'c2', time: '19:00', date: '2024-05-20', format: '2D', price: 110000 },
  { id: 's4', movieId: '2', cinemaId: 'c1', time: '11:00', date: '2024-05-20', format: '2D', price: 110000 },
  { id: 's5', movieId: '2', cinemaId: 'c3', time: '20:00', date: '2024-05-20', format: '3D', price: 130000 },
];

export const COMBOS: Combo[] = [
  {
    id: 'cb1',
    name: 'Solo Combo',
    nameVi: 'Combo Đơn',
    description: '1 Large Popcorn + 1 Large Soda',
    descriptionVi: '1 Bắp Lớn + 1 Nước Lớn',
    price: 85000,
    image: 'https://picsum.photos/seed/combo1/300/300'
  },
  {
    id: 'cb2',
    name: 'Couple Combo',
    nameVi: 'Combo Đôi',
    description: '1 Large Popcorn + 2 Large Sodas',
    descriptionVi: '1 Bắp Lớn + 2 Nước Lớn',
    price: 115000,
    image: 'https://picsum.photos/seed/combo2/300/300'
  },
  {
    id: 'cb3',
    name: 'Family Combo',
    nameVi: 'Combo Gia Đình',
    description: '2 Large Popcorns + 4 Large Sodas',
    descriptionVi: '2 Bắp Lớn + 4 Nước Lớn',
    price: 210000,
    image: 'https://picsum.photos/seed/combo3/300/300'
  }
];
