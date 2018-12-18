# PROFITMINER (PM)

## Custom Vue Components
### pm-responsive-table
Simple table component that add toggle column feature and async loading. Style customization is handled by css.

**How to use:**
```
<pm-responsive-table :rows="rows" :columns="columns"></pm-responsive-table>


export default {
    data() {
        return {
            rows: [
                {
                    id: 1,
                    name: 'name',
                    dealership: {
                        name: 'Dealership'
                    },
                    agency: {
                        name: 'Agency'
                    },
                    recipients_count: 1
                }
            ],
            columns: [
                {
                    field: 'id',
                    is_manager: true,
                    classes: ['font-weight-bold']
                }, {
                    field: 'name'
                }, {
                    field: 'dealership.name',
                    html: true
                }, {
                    field: 'agency.name'
                }, {
                    field: 'recipients_count'
                }, {
                    field: 'options',
                    is_manager_footer: true
                }
            ]
        }
    }
}
```

**Available options for column declaration**

`field` (Required) Row object property that this column corresponds to. This can be: `agency` single row property or `agency.name` nested row property name,

`classes` Array of classes to append to each row column.

`html` indicates whether this column will require html rendering.

`is_manager` indicates that this column shouldn't be hidden on mobile and control toggle feature;

`is_manager_footer` indicates that this column shouldn't be hidden on mobile but can't control toggle feature.

**Available options for cell content customization**

There are two options to customize the cell's content:
1. `html`: this column options treats the content as html
2. Each column has available an [scoped named slot](https://vuejs.org/v2/guide/components-slots.html#Scoped-Slots), the name of the slot match the column's field property. Each slot pass the `row` scoped object. Don't forget that `slot-scope` can actually accept any valid JavaScript expression.

```
<pm-responsive-table :rows="rows" :columns="columns">
    <template slot="options" slot-scope="{row}">
        <button class="btn">A</button>
    </template>
</pm-responsive-table>

export default {
    data() {
        return {
            rows: [
                {
                    id: 1
                }
            ],
            columns: [
                {
                    field: 'id',
                    is_manager: true,
                    classes: ['font-weight-bold']
                }, {
                    field: 'options',
                    is_manager_footer: true
                }
            ]
        }
    }
}
```