CREATE TABLE public.usuarios (
    id SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password TEXT NOT NULL,
    rol VARCHAR(30) NOT NULL CHECK (rol IN ('ingeniero', 'encargado')),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO public.usuarios (id, nombre, email, password, rol, fecha_creacion) VALUES
(1, 'Esteban Rosas', 'estebanrosas@gmail.com', '$2y$10$NdeemtXgNMzK/YqWWEJNueAS7ILB2FA/L8TShMsffx0iM6QJzU8HC', 'ingeniero', '2025-07-30 21:54:59.968468');
