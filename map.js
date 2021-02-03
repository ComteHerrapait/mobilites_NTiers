class Partner {
    constructor(id_, locX_, locY_, name_, city_, country_) {
        this.id = id_;
        this.loc_X = locX_;
        this.loc_Y = locY_;
        this.name = name_;
        this.city = city_;
        this.country = country_;
        this.students = [];
    }
    addStudent(student_) {
        this.students.push(student_);
    }
    getLngLat() {
        return [this.loc_X, this.loc_Y];
    }
    getPopupText() {
        let str = `<h3>${this.name}</h3><div>${this.city}, ${this.country}`;
        str += "<ul>";
        this.students.forEach(function(s) {
            str += `<li>${s.lname}, ${s.fname}</li>`;
        });
        str += "</ul></div>";
        return str;
    }
};

class Student {
    constructor(fname_, lname_) {
        this.fname = fname_;
        this.lname = lname_;
    }
};

class MapMarkers {
    constructor(mobilities_) {
        this.partners = [];
        mobilities_.forEach(mobi => this.createMarker(mobi));
    }
    createMarker(mobility_) {
        if (this.partners.some(p => p.id === mobility_[5])) {
            //partner already exists
            let found = this.partners.find(p => p.id === mobility_[5]);
            let s = new Student(mobility_[3], mobility_[4]);
            found.addStudent(s);
        } else {
            let p = new Partner(mobility_[5], mobility_[1], mobility_[2], mobility_[0], mobility_[6], mobility_[7]);
            let s = new Student(mobility_[3], mobility_[4]);
            p.addStudent(s);
            this.partners.push(p);
        }
    }
};

class MyMap {
    constructor() {
        this.markersOnMap = []
        this.mymap = L.map('mapID')
    }
    draw(mobilities_) {
        this.mymap.setView([0, 0], 3);
        this.mymap.setMaxBounds([
            [-60, -180],
            [80, 180]
        ]);
        L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
            maxZoom: 18,
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors, ' +
                '<a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
                'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1
        }).addTo(this.mymap);
        this.mymap.options.minZoom = 3;
        this.mymap.options.maxZoom = 12;
        var map_markers = new MapMarkers(mobilities);

        var self = this;
        map_markers.partners.forEach(function(p) {
            let marker = new L.marker(p.getLngLat())
                .bindPopup(p.getPopupText())
                .addTo(self.mymap);
            self.markersOnMap.push(marker);
        });
    }
    filter(searchText) {

    }
}