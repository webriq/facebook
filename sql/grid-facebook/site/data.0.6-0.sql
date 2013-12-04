-- remove data

DELETE FROM "module"
      WHERE "module" = 'Grid\Facebook';

DELETE FROM "user_right"
      WHERE "group"     = 'settings'
        AND "resource"  = 'settings.facebook'
        AND "privilege" = 'edit';
