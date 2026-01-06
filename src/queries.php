<?php
declare(strict_types=1);

/**
 * Список “вбудованих” запитів (щоб не дозволяти вводити довільний SQL з браузера).
 * Кожен запит має:
 * - title: назва
 * - sql: текст запиту
 * - params: список параметрів (ім'я => тип)
 */
return [
    'hotels' => [
        'title' => 'Перелік (готелів) за зірковістю і кількістю номерів',
        'sql' => "
            SELECT building_name, hotel_class_stars, rooms_total
            FROM buildings
            ORDER BY hotel_class_stars DESC, building_name;
        ",
        'params' => [],
    ],

    'chargable_servises' => [
        'title' => 'Платні послуги',
        'sql' => "
            SELECT service_name, service_category
            FROM services
            WHERE is_chargeable = TRUE
            ORDER BY service_category, service_name;
        ",
        'params' => [],
    ],

        'Available rooms' => [
        'title' => 'знайти вільні номери на заданий період, у 4–5* корпусах, з місткістю 2–3, з додатковими умовами по поверху/номеру',
        'sql' => "
            SELECT
            b.building_name,
            b.hotel_class_stars AS stars,
            r.room_no,
            r.floor_no,
            r.capacity
            FROM rooms r
            JOIN buildings b ON b.building_id = r.building_id
            WHERE
            r.is_active = TRUE
            AND b.hotel_class_stars IN (4, 5)
            AND r.capacity BETWEEN 2 AND 3

            AND (
                    r.floor_no >= 3
                    OR r.room_no LIKE '5%'
                )

            AND r.room_no NOT LIKE '0%'

            AND NOT EXISTS (
                SELECT 1
                FROM accommodations a
                WHERE a.room_id = r.room_id
                AND a.status IN ('BOOKED','IN_HOUSE')
                -- перевірка перетину інтервалів: [a.check_in, a.check_out) з [:d1, :d2)
                AND a.check_in < :d2
                AND a.check_out > :d1
            )
            ORDER BY stars DESC, b.building_name, r.floor_no, r.room_no;
        ",
        'params' => ['d1' => 'date', 'd2' => 'date'],
    ],

    'free_rooms_now' => [
        'title' => 'Кількість вільних номерів на даний момент',
        'sql' => "
            SELECT COUNT(*) AS free_rooms_now
            FROM rooms r
            WHERE r.is_active = TRUE
              AND NOT EXISTS (
                SELECT 1
                FROM accommodations a
                WHERE a.room_id = r.room_id
                  AND a.status = 'IN_HOUSE'
                  AND NOW() >= a.check_in AND NOW() < a.check_out
              );
        ",
        'params' => [],
    ],

    'free_rooms_by_filters' => [
        'title' => 'Кількість вільних номерів за характеристиками (зірки + місткість)',
        'sql' => "
            SELECT COUNT(*) AS free_rooms_filtered
            FROM rooms r
            JOIN buildings b ON b.building_id = r.building_id
            WHERE r.is_active = TRUE
              AND b.hotel_class_stars = :stars
              AND r.capacity = :capacity
              AND NOT EXISTS (
                SELECT 1
                FROM accommodations a
                WHERE a.room_id = r.room_id
                  AND a.status = 'IN_HOUSE'
                  AND NOW() >= a.check_in AND NOW() < a.check_out
              );
        ",
        'params' => ['stars' => 'int', 'capacity' => 'int'],
    ],

    'unhappy_clients' => [
        'title' => 'Список незадоволених клієнтів та їхні скарги',
        'sql' => "
            SELECT
              g.full_name,
              c.created_at,
              c.status,
              c.complaint_text
            FROM complaints c
            JOIN guests g ON g.guest_id = c.guest_id
            WHERE c.status IN ('NEW','IN_REVIEW')
            ORDER BY g.full_name DESC;
        ",
        'params' => [],
    ],

    'firms_large_bookings' => [
        'title' => 'Фірми з обсягом бронювання (людей) не менше N за період',
        'sql' => "
            SELECT
              o.org_id,
              o.org_name,
              SUM(b.people_count) AS total_people_booked,
              COUNT(*) AS bookings_count
            FROM bookings b
            JOIN organizations o ON o.org_id = b.org_id
            WHERE b.booking_type = 'ORGANIZATION'
              AND b.status IN ('NEW','CONFIRMED','COMPLETED')
              AND b.date_from >= :d1 AND b.date_to <= :d2
            GROUP BY o.org_id, o.org_name
            HAVING SUM(b.people_count) >= :min_people
            ORDER BY total_people_booked DESC;
        ",
        'params' => ['d1' => 'date', 'd2' => 'date', 'min_people' => 'int'],
    ],

    'Avg booking days' => [
        'title' => 'Найменша та найбільша кількість днів бронювання залежно від зірковості готелю',
        'sql' => "
            SELECT
                COALESCE(bld.hotel_class_stars, b.desired_hotel_class_stars) AS stars,
                COUNT(DISTINCT b.booking_id)                                 AS bookings_count,
                MIN(DATEDIFF(b.date_to, b.date_from))                        AS min_days,
                MAX(DATEDIFF(b.date_to, b.date_from))                        AS max_days,
                AVG(DATEDIFF(b.date_to, b.date_from))                        AS avg_days,
                SUM(b.rooms_count)                                          AS total_rooms_booked
            FROM bookings b
                LEFT JOIN booking_rooms br ON br.booking_id = b.booking_id
                LEFT JOIN rooms r          ON r.room_id = br.room_id
                LEFT JOIN buildings bld    ON bld.building_id = r.building_id
            WHERE b.status IN ('NEW','CONFIRMED','COMPLETED')
             AND b.date_to > b.date_from
            GROUP BY stars
            ORDER BY stars;
        ",
        'params' => [],
    ],

       'Search by org' => [
        'title' => 'Пошук бронювань конкретної організації за період (індекс: idx_bookings_org_period)',
        'sql' => "
            SELECT booking_id, date_from, date_to, rooms_count, people_count, status
            FROM bookings
            WHERE org_id = 1
                AND date_from >= '2026-01-01'
                AND date_to   <= '2026-12-31'
            ORDER BY date_from;
        ",
        'params' => [],
    ],

];
