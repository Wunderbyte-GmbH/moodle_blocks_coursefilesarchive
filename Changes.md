History
=============

Version 39.0.2 - TBR
===========================
1. Revert #1 as it might be that an admin may wish to deny a child category even if its parent is allowed.  Solves #2.

If however that is not the case and all children of a parent should be allowed regardless of if the admin has selected the child, then the fix to https://github.com/Wunderbyte-GmbH/moodle_blocks_coursefilesarchive/commit/8dc732650c1ccaf309e8271bc0a8d10b1f043562, being #1 is:

```
    private function check_id_in_subcategories(int $id, int $allowedid): bool {
        if ($id == $allowedid) {
            return true;
        }

        $category = \core_course_category::get($allowedid, MUST_EXIST, true);
        if (!$category->is_uservisible()) {
            return false;
        }

        $subcategories = $category->get_children();
        foreach ($subcategories as $subcategory) {
            if ($this->check_id_in_subcategories($id, $subcategory->id)) {
                return true;
            }
        }
        return false;
    }
```

Version 39.0.1 - TBR
===========================
1. Start of development.
