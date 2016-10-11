(use '[clojure.string :only (split triml)])

;; (let [game-count (Integer/parseInt (read-line))]
;;   (defn single-ladybugs?
;;     [colors ladybugs]
;;     (some #(= 1 (count %))
;;           (vals (group-by #(colors %)
;;                           ladybugs))))

;;   (defn first-happy?
;;     [ladybugs]
;;     (= (first ladybugs)
;;        (first (rest ladybugs))))

;;   (for [i (range 1 (inc game-count))]
;;     (let [cell-count (Integer/parseInt (read-line))
;;           game       (read-line)
;;           ladybugs   (filter #(not= % "_") (split game #""))
;;           colors     (set ladybugs)]
;;       (cond
;;         (zero? (count ladybugs))           (conj responses "YES")
;;         (single-ladybugs? colors ladybugs) (conj responses "NO")
;;         (> cell-count (count ladybugs))    (conj responses "YES")
;;         (first-happy? ladybugs)            (conj responses "YES")
;;         :else                              (conj responses "NO")))))

(let [game-count (Integer/parseInt (read-line))]
  (loop [counter game-count
         cells   []
         games   []]
    (if (> counter 0)
      (recur (dec counter)
             (conj cells (Integer/parseInt (read-line)))
             (conj games (read-line)))
      (apply prn games))))
