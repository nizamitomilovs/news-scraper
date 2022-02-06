<template>
    <div class="users-style">
        <div style="margin-bottom: 20px;">
            <h2>Web News</h2>
        </div>
        <div class="table-style">
            <input class="input" type="text" v-model="search" placeholder="Search by title..."
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
            sortKey: 'title', //initial sorting parameter in alphabetical order
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
            axios.get('/scrape', {params: this.tableShow})
                .then(response => {
                    this.news = response.data.news;
                    this.pagination.total = this.news.length;
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
        paginate(news, pageLength, pageNumber) {
            this.pagination.from = news.length ? ((pageNumber - 1) * pageLength) + 1 : ' ';
            this.pagination.to = pageNumber * pageLength > news.length ? news.length : pageNumber * pageLength;
            this.pagination.prevPage = pageNumber > 1 ? pageNumber : '';
            this.pagination.nextPage = news.length > this.pagination.to ? pageNumber + 1 : '';

            return news.slice((pageNumber - 1) * pageLength, pageNumber * pageLength);
        },
        resetPagination() {
            this.pagination.currentPage = 1;
            this.pagination.prevPage = '';
            this.pagination.nextPage = '';
        },
        sortBy(columnName) {
            this.resetPagination();
            this.sortKey = columnName;
            this.sortOrders[columnName] = this.sortOrders[columnName] * -1;
        },
        getIndex(columnNames, name, points) {
            return columnNames.findIndex(i => i[name] === points)
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
                news = news.filter((post) => {

                    return Object.keys(post).some((searchIndex) => {
                        return String(post[searchIndex]).toLowerCase().indexOf(this.search.toLowerCase()) > -1;
                    })
                });
            }

            return news;
        },
        sortNews() {
            let news = this.news;

            let sortKey = this.sortKey;
            let order = this.sortOrders[sortKey] || 1;
            if (sortKey) {
                news = news.slice().sort((post_1, post_2) => {
                    let index = this.getIndex(this.columns, 'name', sortKey);
                    post_1 = String(post_1[sortKey]).toLowerCase();
                    post_2 = String(post_2[sortKey]).toLowerCase();

                    if (this.columns[index].name && this.columns[index].name === 'date') {
                        return (post_1 === post_2 ? 0 : new Date(post_1).getTime() > new Date(post_2).getTime() ? 1 : -1) * order;
                    }

                    if (this.columns[index].name && this.columns[index].name === 'points') {
                        return (+post_1 === +post_2 ? 0 : +post_1 > +post_2 ? 1 : -1) * order;
                    }

                    return (post_1 === post_2 ? 0 : post_1 > post_2 ? 1 : -1) * order;
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
