-- Add `supabase_id` column to `users` and mark the Laravel migration as run
-- Paste and run this script inside Supabase -> SQL Editor

BEGIN;

-- 1) Add the column if it does not already exist
ALTER TABLE public.users
    ADD COLUMN IF NOT EXISTS supabase_id VARCHAR(255);

-- 2) Create a unique index (if missing)
DO $$
BEGIN
    IF NOT EXISTS (
        SELECT 1 FROM pg_indexes
        WHERE schemaname = 'public' AND indexname = 'users_supabase_id_unique'
    ) THEN
        CREATE UNIQUE INDEX users_supabase_id_unique ON public.users (supabase_id);
    END IF;
END
$$;

-- 3) Ensure the Laravel `migrations` table exists (Laravel uses this to track applied migrations)
CREATE TABLE IF NOT EXISTS public.migrations (
    id serial PRIMARY KEY,
    migration varchar(255) NOT NULL,
    batch integer NOT NULL
);

-- 4) Insert the migration record so Laravel will consider it already applied
--    Update the migration name below if your migration filename differs.
INSERT INTO public.migrations (migration, batch)
SELECT '2025_10_20_120000_add_supabase_id_to_users_table', COALESCE((SELECT MAX(batch) FROM public.migrations), 0) + 1
WHERE NOT EXISTS (
    SELECT 1 FROM public.migrations WHERE migration = '2025_10_20_120000_add_supabase_id_to_users_table'
);

COMMIT;

-- Verification queries (optional):
-- SELECT column_name, data_type FROM information_schema.columns WHERE table_name='users' AND column_name='supabase_id';
-- SELECT * FROM public.migrations WHERE migration LIKE '%add_supabase_id%';
