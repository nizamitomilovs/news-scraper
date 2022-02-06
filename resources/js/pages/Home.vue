<template>
    <div class="users-style">
        <div style="margin-bottom: 20px;">
            <h2>Hacker News</h2>
        </div>
        <div class="table-style">
            <input class="input" type="text" v-model="search" placeholder="Search..."
                   @input="resetPagination()" style="width: 250px;">
            <div class="control">
                <div class="select">
                    <select v-model="length" @change="resetPagination()">
                        <option value="10">10</option>
                        <option value="20">20</option>
                        <option value="30">30</option>
                    </select>
                </div>
            </div>
        </div>
        <table class="table table-bordered table-responsive">
            <thead>
            <tr>
                <th v-for="column in columns" :key="column.name" @click="sortBy(column.name)"
                    :class="sortKey === column.name ? (sortOrders[column.name] > 0 ? 'sorting_asc' : 'sorting_desc') : 'sorting'"
                    style="width: 40%; cursor:pointer;">
                    {{ column.label }}
                </th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="post in paginatedNews" :key="post.id">
                <td v-if="true === isValidHttpUrl(post.link)">
                    <a :href="post.link" target="_blank">{{ post.title }}</a>
                </td>
                <td v-else>{{ post.title }}</td>
                <td>{{ post.points }}</td>
                <td>{{ post.posted_at }}</td>
                <td>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item text-primary" href="#" @click="updatePost(post.id)">Update Points</a>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <div>
            <nav class="pagination" v-if="!tableShow.showdata">
                <span class="page-stats">{{ pagination.from }} - {{ pagination.to }} of {{ pagination.total }}</span>
                <a v-if="pagination.prevPageUrl" class="btn btn-sm btn-primary pagination-previous"
                   @click="--pagination.currentPage"> Prev </a>
                <a class="btn btn-sm btn-primary pagination-previous" v-else disabled> Prev </a>
                <a v-if="pagination.nextPageUrl" class="btn btn-sm pagination-next" @click="++pagination.currentPage">
                    Next </a>
                <a class="btn btn-sm btn-primary pagination-next" v-else disabled> Next </a>
            </nav>
            <nav class="pagination" v-else>
<span class="page-stats">
{{ pagination.from }} - {{ pagination.to }} of {{ filteredNews.length }}
<span v-if="`filteredNews.length < pagination.total`"></span>
</span>
                <a v-if="pagination.prevPage" class="btn btn-sm btn-primary pagination-previous"
                   @click="--pagination.currentPage"> Prev </a>
                <a class="btn btn-sm pagination-previous btn-primary" v-else disabled> Prev </a>
                <a v-if="pagination.nextPage" class="btn btn-sm btn-primary pagination-next"
                   @click="++pagination.currentPage"> Next </a>
                <a class="btn btn-sm pagination-next btn-primary" v-else disabled> Next </a>
            </nav>
        </div>
    </div>
</template>
<script>
export default {
    created() {
        this.getNews();
        Fire.$on('reloadNews', () => {
            this.getNews();
        })
    },
    data() {
        let sortOrders = {};
        let columns = [
            {label: 'Title', name: 'title'},
            {label: 'Points', name: 'points'},
            {label: 'Date Added', name: 'posted_at'},
        ];
        columns.forEach((column) => {
            sortOrders[column.title] = -1;
        });
        return {
            news: [],
            columns: columns,
            sortKey: 'points',
            sortOrders: sortOrders,
            length: 10,
            search: '',
            tableShow: {
                showdata: true,
            },
            pagination: {
                currentPage: 1,
                total: '',
                nextPage: '',
                prevPage: '',
                from: '',
                to: ''
            },
        }
    },
    methods: {
        updatePost(id) {
            axios.post(`/api/post/points/update`).then(() => {
                Fire.$emit('reloadNews')
                swal(
                    'Success!',
                    'Post updated',
                    'success'
                )
            }).catch(() => {
                swal('Failed', 'There was something wrong', 'warning');
            });
        },
        getNews() {
            axios.get('/api/scrape/', {params: this.tableShow})
                .then(response => {
                    this.news = response.data.news;
                    this.pagination.total = this.news.length;
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
        paginate(array, length, pageNumber) {
            this.pagination.from = array.length ? ((pageNumber - 1) * length) + 1 : ' ';
            this.pagination.to = pageNumber * length > array.length ? array.length : pageNumber * length;
            this.pagination.prevPage = pageNumber > 1 ? pageNumber : '';
            this.pagination.nextPage = array.length > this.pagination.to ? pageNumber + 1 : '';
            return array.slice((pageNumber - 1) * length, pageNumber * length);
        },
        resetPagination() {
            this.pagination.currentPage = 1;
            this.pagination.prevPage = '';
            this.pagination.nextPage = '';
        },
        sortBy(key) {
            this.resetPagination();
            this.sortKey = key;
            this.sortOrders[key] = this.sortOrders[key] * -1;
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] === value)
        },
        isValidHttpUrl(string) {
            let url;
            try {
                url = new URL(string);
            } catch (_) {
                return false;
            }

            return url.protocol === "http:" || url.protocol === "https:";
        }
    },
    computed: {
        filteredNews() {
            let news = this.news;
            if (this.search) {
                news = news.filter((row) => {
                    return Object.keys(row).some((key) => {
                        return String(row[key]).toLowerCase().indexOf(this.search.toLowerCase()) > -1;
                    })
                });
            }
            let sortKey = this.sortKey;
            let order = this.sortOrders[sortKey] || 1;

            if (sortKey) {
                news = news.slice().sort((a, b) => {
                    let index = this.getIndex(this.columns, 'name', sortKey);
                    a = String(a[sortKey]).toLowerCase();
                    b = String(b[sortKey]).toLowerCase();
                    if (this.columns[index].type && this.columns[index].type === 'date') {

                        return (a === b ? 0 : new Date(a).getTime() > new Date(b).getTime() ? 1 : -1) * order;
                    } else if (this.columns[index].type && this.columns[index].type === 'number') {
                        return (+a === +b ? 0 : +a > +b ? 1 : -1) * order;
                    } else {
                        return (a === b ? 0 : a > b ? 1 : -1) * order;
                    }
                });
            }

            return news;
        },
        paginatedNews() {
            return this.paginate(this.filteredNews, this.length, this.pagination.currentPage);
        },
    }
};
</script>
